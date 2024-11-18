import socket
import config as cfg

def start_server() -> None:
    """fonction de test pour l'écoute d'un client sur le port 6666
    """
    #internal variables
    data: str
    server_socket: socket.socket
    conn: socket.socket
    addr: str
    
    
    # server socket setup
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind((cfg.SERVER_IP, cfg.SERVER_PORT))
    server_socket.listen(1)
    print("Serveur en attente de connexion...")

    # waiting for client connection
    conn, addr = server_socket.accept()
    print(f"Connexion établie avec {addr} \n En attente d'ID...")

    # communication with client
    with conn:

        while True:
            # ID received
            # conn.sendall("ID ? (exit pour fermer la connexion)\n".encode())
            data = conn.recv(1024).decode().strip()  # .strip() to remove the newline and space characters
            if not data:
                print("No data received, closing connection.")
                break
            print(f"ID received = '{data}'")
            
            # ID check
            if data == "abcde123456":
                conn.sendall("id ok\n".encode())
                
                # Receveing the size of the file
                data = conn.recv(1024).decode().strip()
                if not data:
                    print("No data received, closing connection.")
                    break
                print(f"size received = '{data}'")
                
                if data == "10ko":
                    conn.sendall("volume ok\n".encode())
                    
                    # Réception des données envoyées
                    data = conn.recv(1024).decode().strip()
                    if not data:
                        print("No data received, closing connection.")
                        break
                    print(f"data received = '{data}'")
                    
                    if data == "donnee123456":
                        conn.sendall("transfert ok\n".encode())
                        print("transfert successful.")
                    else:
                        conn.sendall("transfert error\n".encode())
                        print("Tansfert error.")
                        break
                else:
                    conn.sendall("fichier trop volumineux\n".encode())
                    print("file too big.")
                    break
            elif data == "exit":
                conn.sendall("closing connection\n".encode())
                print("closing connection.")
                break
            else:
                conn.sendall("ID error\n".encode())
                print("ID Error.")

    # closing socket
    server_socket.close()

if __name__ == "__main__":
    start_server()
