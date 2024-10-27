"""
ce fichier sert à stocker les variables d'environnement du serveur de manière sécurisée
"""

import os
from dotenv import load_dotenv

# on charge le fichier .env
load_dotenv()

# définition des variables d'environnement
SERVER_IP = os.getenv("SERVER_IP")
SERVER_PORT = int(os.getenv("SERVER_PORT"))
LOGIN = os.getenv("LOGIN")
PW = os.getenv("PASSWORD")
DBNAME = os.getenv("DBNAME")