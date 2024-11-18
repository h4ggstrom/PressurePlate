import java.net.*;

import xmlClass.XMLContent;

import java.io.*;

public class TCP_Client {
    private Socket clientSocket;
    private PrintWriter out;
    private BufferedReader in;

    public void start(String ip, int port) throws Exception {
        clientSocket = new Socket(ip, port);
        out = new PrintWriter(clientSocket.getOutputStream(), true);
        in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));

        XMLContent xml_content = new XMLContent();
        xml_content.addEmployees("e_00000006","Lachaise","Jean","Caissier");

        System.out.println("Envoi : abcde123456");
        out.println("abcde123456");
        
        String id = in.readLine();
        System.out.println("Reçu : " + id);
        
        if(id.equals("id ok")) {
            System.out.println("Envoi : 10ko");
            out.println("10ko");
            
            String volume = in.readLine();
            System.out.println("Reçu : " + volume);
            
            if(volume.equals("volume ok")) {
                System.out.println(xml_content.getXMLContent());
                out.println(xml_content.getXMLContent());
                
                String transfert = in.readLine();
                System.out.println("Reçu : " + transfert);
                
                if (transfert.equals("transfert ok")) {
                    System.out.println("Envoi : succces du transfert, fermeture...");
                    out.println("succces du transfert, fermeture...");
                    System.out.println("Succes transfert");
                } else {
                    System.out.println("Envoi : transfert error");
                    out.println("transfert error");
                    throw new Exception("Echec Transfert");
                }
            } else {
                System.out.println("Envoi : fichier trop volumineux");
                out.println("fichier trop volumineux");
                throw new Exception("Fichier trop volumineux");
            }
        } else {
            System.out.println("Envoi : ID error");
            out.println("ID error");
            throw new Exception("ID Error");
        }
    }

    public void stop() throws IOException {
        in.close();
        out.close();
        clientSocket.close();
    }

    public static void main(String[] args) {
        TCP_Client client = new TCP_Client();
        try {
            client.start("localhost", 6666); // Connexion à ncat en tant que client
            // System.out.println("Client lancé");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
