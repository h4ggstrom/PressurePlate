import socket

def start_server():
    # Crée un socket TCP
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    
    # Attache le socket à localhost sur le port 6666
    server_socket.bind(('localhost', 6666))
    
    # Met le socket en mode écoute
    server_socket.listen(1)
    print("Serveur en attente de connexion...")

    # Accepte une connexion du client
    conn, addr = server_socket.accept()
    print(f"Connexion établie avec {addr} \n En attente d'ID...")

    with conn:
        # Boucle de communication
        while True:
            # Réception de l'ID envoyé par le client
            data = conn.recv(1024).decode().strip()  # .strip() pour enlever les espaces superflus
            if not data:
                print("Aucune donnée reçue, fermeture de la connexion.")
                break
            print(f"ID reçu = '{data}'")  # Affiche le message reçu avec des quotes pour débogage
            
            # Vérification de l'ID
            if data == "abcde123456":
                conn.sendall("id ok".encode())
                
                # Réception de la taille du fichier (ou similaire)
                data = conn.recv(1024).decode().strip()
                if not data:
                    print("Aucune donnée reçue, fermeture de la connexion.")
                    break
                print(f"Taille reçue = '{data}'")
                
                if data == "10ko":
                    conn.sendall("volume ok".encode())
                    
                    # Réception des données envoyées
                    data = conn.recv(1024).decode().strip()
                    if not data:
                        print("Aucune donnée reçue, fermeture de la connexion.")
                        break
                    print(f"Données reçues = '{data}'")
                    
                    if data == "donnee123456":
                        conn.sendall("transfert ok".encode())
                        print("Transfert réussi.")
                    else:
                        conn.sendall("transfert error".encode())
                        print("Erreur de transfert.")
                        break
                else:
                    conn.sendall("fichier trop volumineux".encode())
                    print("Fichier trop volumineux.")
                    break
            elif data == "exit":
                conn.sendall("fermeture de la connexion".encode())
                print("Fermeture de la connexion demandée par le client.")
                break
            else:
                conn.sendall("ID error".encode())
                print("Erreur d'ID.")

    # Ferme le socket serveur
    server_socket.close()

if __name__ == "__main__":
    start_server()
