import socket

def start_server() -> None:
    """fonction de test pour l'écoute d'un client sur le port 6666
    """
    #variables internes
    data: str
    server_socket: socket.socket
    conn: socket.socket
    addr: str
    
    
    # setup du socket serveur, sur le port 6666 en mode écoute
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind(('localhost', 6666))
    server_socket.listen(1)
    print("Serveur en attente de connexion...")

    # Accepte une connexion du client
    conn, addr = server_socket.accept()
    print(f"Connexion établie avec {addr} \n En attente d'ID...")

    # boucle de dialogue avec le client
    with conn:

        while True:
            # Réception de l'ID 
            # conn.sendall("ID ? (exit pour fermer la connexion)\n".encode())
            data = conn.recv(1024).decode().strip()  # .strip() pour enlever les espaces superflus
            if not data:
                print("Aucune donnée reçue, fermeture de la connexion.")
                break
            print(f"ID reçu = '{data}'")
            
            # Vérification de l'ID
            if data == "abcde123456":
                conn.sendall("id ok\n".encode())
                
                # Réception de la taille du fichier
                data = conn.recv(1024).decode().strip()
                if not data:
                    print("Aucune donnée reçue, fermeture de la connexion.")
                    break
                print(f"Taille reçue = '{data}'")
                
                if data == "10ko":
                    conn.sendall("volume ok\n".encode())
                    
                    # Réception des données envoyées
                    data = conn.recv(1024).decode().strip()
                    if not data:
                        print("Aucune donnée reçue, fermeture de la connexion.")
                        break
                    print(f"Données reçues = '{data}'")
                    
                    if data == "donnee123456":
                        conn.sendall("transfert ok\n".encode())
                        print("Transfert réussi.")
                    else:
                        conn.sendall("transfert error\n".encode())
                        print("Erreur de transfert.")
                        break
                else:
                    conn.sendall("fichier trop volumineux\n".encode())
                    print("Fichier trop volumineux.")
                    break
            elif data == "exit":
                conn.sendall("fermeture de la connexion\n".encode())
                print("Fermeture de la connexion demandée par le client.")
                break
            else:
                conn.sendall("ID error\n".encode())
                print("Erreur d'ID.")

    # Ferme le socket serveur
    server_socket.close()

if __name__ == "__main__":
    start_server()
