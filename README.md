# Garage


8️⃣ Déploiement sur un nouveau serveur

Sur un nouveau serveur :
creer un utilisateur "sami"

git clone https://github.com/masmoudisami/garage-v3.git

cd test

docker compose up -d --build

////////////

import fichier sql

1️⃣ Ouvre :

http://localhost:8081

2️⃣ Connecte-toi :

user : sami
password : 

3️⃣ Clique sur la base : mechanic_db

4️⃣ Onglet Import

5️⃣ Importer ton fichier : mechanic_db.sql


Application accessiblr via : http://localhost:8080

///////////

7️⃣ Sauvegarde de la base (très important)

Backup manuel :

docker exec mysql_db mysqldump -u root -p131301 mechanic_db > backup_mechanic_db.sql











