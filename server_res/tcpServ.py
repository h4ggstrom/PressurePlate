import socket
from loguru import logger
from config import SERVER_IP, SERVER_PORT, LOGIN, PW, DBNAME, DB_IP, DB_PORT
import psycopg2

# Configurer Loguru pour tracer les actions dans un fichier
logger.add("server.log", format="{time} {level} {message}", level="DEBUG")

BUFFER_SIZE = 1024  # Taille maximale des paquets pour éviter les dépassements de tampon
PROTOCOL_VERSION = "1.0"  # Version de protocole pour validation future

# Fonction pour vérifier si l'ID Client est valide
def is_valid_client_id(client_id):
    try:
        # Connexion à la base de données
        conn = psycopg2.connect(
            dbname=DBNAME,
            user=LOGIN,
            password=PW,
            host=DB_IP,
            port=DB_PORT
        )
        cur = conn.cursor()
        
        # Exécuter une requête pour vérifier l'existence de l'ID
        query = "SELECT COUNT(*) FROM installations WHERE centrale_id = %s"
        cur.execute(query, (client_id,))
        result = cur.fetchone()[0]
        
        # Nettoyage
        cur.close()
        conn.close()
        
        return result > 0
    except Exception as e:
        logger.error(f"Erreur lors de la vérification de l'ID Client : {e}")
        return False

# Fonction pour gérer chaque client
def handle_client(client_socket, client_address):
    logger.info(f"Connexion établie avec {client_address}")
    
    try:
        # Étape 1 : Réception de l'ID Client
        client_id = client_socket.recv(BUFFER_SIZE).decode().strip()
        if not client_id:
            logger.warning("ID Client vide reçu.")
            client_socket.close()
            return
        
        # Vérification de l'ID Client dans la base de données
        if not is_valid_client_id(client_id):
            logger.warning(f"ID Client invalide : {client_id}")
            client_socket.send(b"ID invalide")
            client_socket.close()
            return
        
        logger.info(f"ID Client valide reçu : {client_id}")
        client_socket.send(b"ID OK")

        # Étape 2 : Annonce d'intention
        intention = client_socket.recv(BUFFER_SIZE).decode().strip()
        if intention != "stats":
            logger.warning(f"Intention invalide reçue : {intention}")
            client_socket.send(b"Intention invalide")
            client_socket.close()
            return
        logger.info("Intention valide reçue : stats")
        client_socket.send(b"Intention valide")

        # Étape 3 : Réception du fichier CSV
        logger.info("en attente de la réception du fichier CSV...")
        file_data = b""
        while True:
            chunk = client_socket.recv(BUFFER_SIZE)
            if not chunk:  # Pas de données supplémentaires
                break
            file_data += chunk
            # Ajout d'une vérification contre les dépassements
            if len(file_data) > 10 * 1024 * 1024:  # 10 MB max
                logger.error("Fichier trop volumineux reçu, connexion coupée.")
                client_socket.send(b"Fichier trop volumineux")
                client_socket.close()
                return

        # Étape 4 : Confirmation de réception
        logger.info("Fichier CSV reçu avec succès.")
        client_socket.send("Fichier reçu")

        # Étape 5 : Annonce de clôture
        closure_announcement = client_socket.recv(BUFFER_SIZE).decode().strip()
        if closure_announcement != "close":
            logger.warning(f"Annonce de clôture invalide : {closure_announcement}")
            client_socket.send("Clôture invalide")
            client_socket.close()
            return
        logger.info("Annonce de clôture reçue.")
        client_socket.send("Clôture confirmée")
        
        # Sauvegarder le fichier reçu (dans un dossier temporaire pour l'instant)
        with open("received_stats.csv", "wb") as f:
            f.write(file_data)
        logger.info("Fichier CSV sauvegardé sous 'received_stats.csv'.")

    except Exception as e:
        logger.error(f"Erreur lors de la gestion du client {client_address} : {e}")
    finally:
        client_socket.close()
        logger.info(f"Connexion avec {client_address} fermée.")

# Fonction principale pour lancer le serveur
def start_server():
    logger.info("Démarrage du serveur...")
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind((SERVER_IP, SERVER_PORT))
    server_socket.listen(5)  # Limiter à 5 connexions simultanées
    logger.info(f"Serveur en écoute sur {SERVER_IP}:{SERVER_PORT}")

    try:
        while True:
            client_socket, client_address = server_socket.accept()
            handle_client(client_socket, client_address)
    except KeyboardInterrupt:
        logger.info("Arrêt du serveur via interruption clavier.")
    finally:
        server_socket.close()
        logger.info("Serveur arrêté.")

if __name__ == "__main__":
    start_server()
