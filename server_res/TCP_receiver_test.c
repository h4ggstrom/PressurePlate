/*
 * Ceci est une ebauche de server TCP simple qui reçoit des donnees d'un client.
 * Il a pour but de se familiariser, et d'avoir un server "foncitonnel" pour les tests du client.
 * La version finale sera basée sur celui la, mais structurellement, il sera sans doute TRES différent.
 *
 * Pour l'instant, ce fichier est code de maniere a pouvoir etre compile et execute sur windows aussi bien que linux.
 * Une fois qu'on saura exactement sur quel OS on va travailler, on pourra simplifier le code en enlevant les parties inutiles.
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#ifdef _WIN32
    #include <winsock2.h>
    #pragma comment(lib, "ws2_32.lib")  // Lier la bibliothèque Winsock
    typedef int socklen_t;  // Définir socklen_t comme int sous Windows
#else
    #include <unistd.h>
    #include <arpa/inet.h>
    #include <sys/socket.h>
#endif

#define PORT 8080  // Port sur lequel le serveur écoutera
#define BUFFER_SIZE 1024  // Taille du buffer de réception

void platform_init() {
#ifdef _WIN32
    WSADATA wsa;
    printf("Initialisation de Winsock...\n");
    if (WSAStartup(MAKEWORD(2, 2), &wsa) != 0) {
        printf("Échec de l'initialisation. Code d'erreur : %d\n", WSAGetLastError());
        exit(EXIT_FAILURE);
    }
    printf("Initialisation réussie.\n");
#endif
}

void platform_cleanup() {
#ifdef _WIN32
    WSACleanup();
#endif
}

int main() {
    platform_init();  // Initialiser Winsock sur Windows (inutile sous Linux)

    int server_fd, new_socket;
    struct sockaddr_in address;
    int addrlen = sizeof(address);
    char buffer[BUFFER_SIZE] = {0};

    // Création du socket
    if ((server_fd = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
#ifdef _WIN32
        printf("Échec de la création du socket. Code d'erreur : %d\n", WSAGetLastError());
#else
        perror("Échec de la création du socket");
#endif
        platform_cleanup();
        exit(EXIT_FAILURE);
    }

    // Configuration de l'adresse du serveur
    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(PORT);

    // Bind le socket à l'adresse spécifiée
    if (bind(server_fd, (struct sockaddr *)&address, sizeof(address)) < 0) {
#ifdef _WIN32
        printf("Échec du bind. Code d'erreur : %d\n", WSAGetLastError());
#else
        perror("Échec du bind");
#endif
        platform_cleanup();
        exit(EXIT_FAILURE);
    }

    // Mettre le serveur en mode écoute
    if (listen(server_fd, 3) < 0) {
#ifdef _WIN32
        printf("Échec de l'écoute. Code d'erreur : %d\n", WSAGetLastError());
#else
        perror("Échec de l'écoute");
#endif
        platform_cleanup();
        exit(EXIT_FAILURE);
    }

    printf("Serveur en écoute sur le port %d...\n", PORT);

    // Accepter une connexion entrante
    if ((new_socket = accept(server_fd, (struct sockaddr *)&address, (socklen_t*)&addrlen)) < 0) {
#ifdef _WIN32
        printf("Échec de l'acceptation de la connexion. Code d'erreur : %d\n", WSAGetLastError());
#else
        perror("Échec de l'acceptation de la connexion");
#endif
        platform_cleanup();
        exit(EXIT_FAILURE);
    }

    // Lire les données envoyées par le client
    int bytes_received = recv(new_socket, buffer, BUFFER_SIZE, 0);
    if (bytes_received < 0) {
#ifdef _WIN32
        printf("Erreur lors de la réception des données. Code d'erreur : %d\n", WSAGetLastError());
#else
        perror("Erreur lors de la réception des données");
#endif
        platform_cleanup();
        exit(EXIT_FAILURE);
    }

    printf("Données reçues : %s\n", buffer);

    // Fermer les sockets
#ifdef _WIN32
    closesocket(new_socket);
    closesocket(server_fd);
#else
    close(new_socket);
    close(server_fd);
#endif

    platform_cleanup();  // Nettoyer Winsock sous Windows

    return 0;
}
