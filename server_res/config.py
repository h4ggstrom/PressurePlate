"""
ce fichier sert à stocker les variables d'environnement du serveur de manière sécurisée
"""

import os
from dotenv import load_dotenv

# on charge le fichier .env
load_dotenv(dotenv_path='C:\\Users\\robin\\Desktop\\rep\\PressurePlate\\.env')

# définition des variables d'environnement
SERVER_IP = os.getenv("SERVER_IP")
SERVER_PORT =int(os.getenv("SERVER_PORT"))
LOGIN = os.getenv("LOGIN")
PW = os.getenv("PASSWORD")
DBNAME = os.getenv("DBNAME")

# Vérification des variables d'environnement
if not all([SERVER_IP, SERVER_PORT, LOGIN, PW, DBNAME]):
    raise EnvironmentError("Une ou plusieurs variables d'environnement ne sont pas définies.")