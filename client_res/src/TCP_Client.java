import java.net.*;
import java.nio.file.Paths;
import java.util.Scanner;

import javax.print.DocFlavor.STRING;

import java.io.*;

public class TCP_Client {
    private Socket clientSocket;
    private String clientID;
    private PrintWriter out;
    private BufferedReader in;

    public void start(String ip, int port) throws Exception {
        clientSocket = new Socket(ip, port);
        out = new PrintWriter(clientSocket.getOutputStream(), true);
        in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));
        this.clientID = readClientID();
        String csvString = new CSVContent("csv_ressource\\csv_file.csv").getCSVString();
        System.out.println("Envoi :" + this.clientID);
        out.println(this.clientID);

        String id = in.readLine();
        System.out.println("Reçu : " + id);

        if (id.equals("id ok")) {
            System.out.println("Envoi : 10ko");
            out.println("10ko");

            String volume = in.readLine();
            System.out.println("Reçu : " + volume);

            if (volume.equals("volume ok")) {
                System.out.println(csvString);
                out.println(csvString);
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
            client.start("localhost", 24816 ); // Connexion à ncat en tant que client
            // System.out.println("Client lancé");
        }catch (UnknownHostException e) {
            e.printStackTrace();
        }
        catch(IllegalArgumentException e){
            e.printStackTrace();
        }
        catch(SecurityException e){
            e.printStackTrace();
        }
         catch (Exception e) {
            e.printStackTrace();
        }
    }

    public String readClientID() {
        try {
            URL resource = CSVContent.class.getResource("conf.txt");
            File csvFile = Paths.get(resource.toURI()).toFile();
            Scanner csvReader = new Scanner(csvFile);
            String clientID = csvReader.nextLine();
            csvReader.close();
            return clientID;
        } catch (Exception e) {
            System.out.println("An error occurred.");
            e.printStackTrace();
            return "";
        }
    }
}
