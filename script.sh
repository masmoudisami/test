#!/bin/bash

# ============================================================
#  setup.sh — Installation automatique du projet Garage
#  Compatible : Ubuntu / Debian | root ou utilisateur sudo
# ============================================================

set -e

# ── Couleurs ────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}[OK]${NC}    $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# ── Détection root / sudo ───────────────────────────────────
if [ "$EUID" -eq 0 ]; then
    SUDO=""
    info "Exécution en tant que root."
else
    SUDO="sudo"
    info "Exécution en tant qu'utilisateur $(whoami) — sudo sera utilisé."
    sudo -v || error "Impossible d'obtenir les droits sudo."
fi

# ============================================================
# 1. Mise à jour du système
# ============================================================
info "Mise à jour des paquets système..."
$SUDO apt-get update -y
$SUDO apt-get upgrade -y
success "Système mis à jour."

# ============================================================
# 2. Installation de Docker (si absent)
# ============================================================
if command -v docker &>/dev/null; then
    success "Docker est déjà installé : $(docker --version)"
else
    info "Installation de Docker..."

    $SUDO apt-get install -y \
        ca-certificates curl gnupg lsb-release git

    # Ajout de la clé GPG officielle Docker
    $SUDO install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/$(. /etc/os-release && echo "$ID")/gpg \
        | $SUDO gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    $SUDO chmod a+r /etc/apt/keyrings/docker.gpg

    # Ajout du dépôt Docker
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
      https://download.docker.com/linux/$(. /etc/os-release && echo "$ID") \
      $(lsb_release -cs) stable" \
      | $SUDO tee /etc/apt/sources.list.d/docker.list > /dev/null

    $SUDO apt-get update -y
    $SUDO apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Démarrage et activation du service
    $SUDO systemctl enable docker
    $SUDO systemctl start docker

    # Ajout de l'utilisateur courant au groupe docker (si non-root)
    if [ "$EUID" -ne 0 ]; then
        $SUDO usermod -aG docker "$USER"
        warn "Utilisateur '$USER' ajouté au groupe docker."
        warn "Reconnectez-vous (ou lancez 'newgrp docker') pour appliquer le changement."
        # Utiliser sg pour continuer sans reconnexion dans ce script
        DOCKER_CMD="sg docker -c docker"
    fi

    success "Docker installé : $(docker --version)"
fi

# Alias docker pour la suite du script
DOCKER_CMD="${DOCKER_CMD:-docker}"

# Installation de docker-compose standalone si la commande manque
if ! command -v docker-compose &>/dev/null && ! docker compose version &>/dev/null 2>&1; then
    info "Installation de docker-compose..."
    $SUDO curl -SL "https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$(uname -m)" \
        -o /usr/local/bin/docker-compose
    $SUDO chmod +x /usr/local/bin/docker-compose
    success "docker-compose installé."
fi

# Déterminer la commande compose disponible
if docker compose version &>/dev/null 2>&1; then
    COMPOSE="docker compose"
elif command -v docker-compose &>/dev/null; then
    COMPOSE="docker-compose"
else
    error "Impossible de trouver docker compose."
fi

# ============================================================
# 3. Clonage du dépôt GitHub
# ============================================================
APP_DIR="$HOME/garage"

if [ -d "$APP_DIR/.git" ]; then
    info "Le dépôt existe déjà — mise à jour (git pull)..."
    git -C "$APP_DIR" pull
else
    info "Clonage du dépôt https://github.com/masmoudisami/garage.git ..."
    git clone https://github.com/masmoudisami/garage.git "$APP_DIR"
fi
success "Dépôt disponible dans $APP_DIR."

# ============================================================
# Configuration — copie/mise à jour des fichiers fournis
# ============================================================
info "Copie des fichiers de configuration..."

# .env → racine du projet
cp "$(dirname "$0")/.env" "$APP_DIR/.env" 2>/dev/null \
    || warn "Fichier .env non trouvé à côté du script — le .env existant sera utilisé."

# config.php → racine ou sous-dossier selon la structure du projet
if [ -f "$APP_DIR/config.php" ]; then
    cp "$(dirname "$0")/config.php" "$APP_DIR/config.php" 2>/dev/null \
        || warn "config.php non trouvé à côté du script."
fi

# Charger les variables du .env
set -a
# shellcheck disable=SC1090
source "$APP_DIR/.env"
set +a

# Variables attendues (avec valeurs par défaut issues du .env fourni)
DB_NAME="${MYSQL_DATABASE:-mechanic_db}"
DB_USER="${MYSQL_USER:-appuser}"
DB_PASS="${MYSQL_PASSWORD:-secret}"
DB_ROOT_PASS="${MYSQL_ROOT_PASSWORD:-rootpass}"
MYSQL_CONTAINER=""   # sera détecté après démarrage

# ============================================================
# 4. Construction et démarrage des conteneurs
# ============================================================
info "Construction et démarrage des conteneurs Docker..."
cd "$APP_DIR"

# Arrêter d'éventuels conteneurs existants proprement
$COMPOSE down --remove-orphans 2>/dev/null || true

$COMPOSE up -d --build

success "Conteneurs démarrés."
$COMPOSE ps

# ============================================================
# 5. Import de la base de données (init.sql)
# ============================================================
SQL_FILE="$APP_DIR/mysql/init.sql"

if [ ! -f "$SQL_FILE" ]; then
    error "Fichier SQL introuvable : $SQL_FILE"
fi

# Identifier le nom du conteneur MySQL/MariaDB
info "Recherche du conteneur MySQL/MariaDB..."
MYSQL_CONTAINER=$(docker ps --format '{{.Names}}' | grep -Ei 'mysql|mariadb|db' | head -n1)

if [ -z "$MYSQL_CONTAINER" ]; then
    error "Aucun conteneur MySQL/MariaDB détecté. Vérifiez votre docker-compose.yml."
fi
info "Conteneur base de données détecté : $MYSQL_CONTAINER"

# ── Attendre que MySQL soit prêt ───────────────────────────
info "Attente de la disponibilité de MySQL (max 90s)..."
MAX_WAIT=90
WAITED=0
until docker exec "$MYSQL_CONTAINER" mysqladmin ping -h 127.0.0.1 --silent 2>/dev/null; do
    sleep 2
    WAITED=$((WAITED + 2))
    if [ "$WAITED" -ge "$MAX_WAIT" ]; then
        error "MySQL n'a pas démarré dans les ${MAX_WAIT}s.\nLogs : docker logs $MYSQL_CONTAINER"
    fi
    echo -n "."
done
echo ""
success "MySQL est prêt."

# ── Écriture d'un .my.cnf sécurisé dans le conteneur ──────
# Cela évite le warning "password on CLI" et isole bien les credentials.
write_mycnf() {
    local user="$1" pass="$2"
    docker exec "$MYSQL_CONTAINER" bash -c "cat > /root/.my.cnf <<EOF
[client]
host=127.0.0.1
user=${user}
password=${pass}
EOF
chmod 600 /root/.my.cnf"
}

# Fonction test : retourne 0 si la connexion réussit
try_mycnf() {
    docker exec "$MYSQL_CONTAINER" mysqladmin status 2>/dev/null | grep -q "Uptime"
}

info "Détection des credentials MySQL..."

AUTHED=0

# Cas 1 : root avec MYSQL_ROOT_PASSWORD du .env
write_mycnf "root" "$DB_ROOT_PASS"
if try_mycnf; then
    info "Auth OK : root / MYSQL_ROOT_PASSWORD"
    AUTHED=1
fi

# Cas 2 : root sans mot de passe
if [ "$AUTHED" -eq 0 ]; then
    write_mycnf "root" ""
    if try_mycnf; then
        info "Auth OK : root sans mot de passe"
        AUTHED=1
    fi
fi

# Cas 3 : utilisateur applicatif du .env
if [ "$AUTHED" -eq 0 ]; then
    write_mycnf "$DB_USER" "$DB_PASS"
    if try_mycnf; then
        warn "Auth root échouée — utilisation de '${DB_USER}'"
        AUTHED=1
    fi
fi

# Cas 4 : saisie manuelle
if [ "$AUTHED" -eq 0 ]; then
    warn "Aucun credential automatique ne fonctionne."
    echo -n "  Entrez le mot de passe root MySQL du conteneur : "
    read -rs MANUAL_PASS
    echo ""
    write_mycnf "root" "$MANUAL_PASS"
    if try_mycnf; then
        success "Auth manuelle acceptée."
        AUTHED=1
    else
        # Afficher les logs pour aider au diagnostic
        echo ""
        warn "=== Dernières lignes de logs du conteneur MySQL ==="
        docker logs "$MYSQL_CONTAINER" 2>&1 | tail -20
        error "Accès MySQL refusé. Consultez les logs ci-dessus."
    fi
fi

# ── S'assurer que la base de données cible existe ──────────
info "Vérification / création de la base '$DB_NAME'..."
docker exec "$MYSQL_CONTAINER" mysql \
    -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" \
    2>/dev/null || warn "La base existe peut-être déjà, on continue."

# ── Import du fichier SQL via docker cp (évite les problèmes de stdin) ─
info "Copie de init.sql dans le conteneur..."
docker cp "$SQL_FILE" "$MYSQL_CONTAINER:/tmp/init.sql"

info "Import de init.sql dans la base '$DB_NAME'..."
docker exec "$MYSQL_CONTAINER" \
    bash -c "mysql \`${DB_NAME}\` < /tmp/init.sql && rm /tmp/init.sql"

# Nettoyage du .my.cnf temporaire
docker exec "$MYSQL_CONTAINER" rm -f /root/.my.cnf

success "Base de données importée avec succès."

# ============================================================
# Résumé final
# ============================================================
echo ""
echo -e "${GREEN}============================================================${NC}"
echo -e "${GREEN}  Installation terminée avec succès !${NC}"
echo -e "${GREEN}============================================================${NC}"
echo ""
echo -e "  Projet   : ${CYAN}$APP_DIR${NC}"
echo -e "  DB Name  : ${CYAN}$DB_NAME${NC}"
echo -e "  DB User  : ${CYAN}$DB_USER${NC}"
echo ""
echo -e "  Vérifier les conteneurs : ${YELLOW}docker ps${NC}"
echo -e "  Logs application        : ${YELLOW}cd $APP_DIR && $COMPOSE logs -f${NC}"
echo ""
