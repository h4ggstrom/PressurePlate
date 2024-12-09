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

        if (id.equals("ID OK")) {
            System.out.println("Envoi : stats");
            out.println("stats");

            String intention = in.readLine();
            System.out.println("Reçu : " + intention);

            if (intention.equals("Intention valide")) {
                System.out.println(csvString);
                out.println(csvString);
                String transfert = in.readLine();
                System.out.println("Reçu : " + transfert);

                if (transfert.equals("Fichier recu")) {
                    System.out.println("Envoi : succces du transfert, fermeture...");
                    out.println("close");
                    System.out.println("Succes transfert");
                } else {
                    System.out.println("Envoi : transfert error");
                    out.println("close");
                    throw new Exception("Echec Transfert");
                }

                


            } else {
                System.out.println("Envoi : Erreur d'intention");
                out.println("Erreur d'intention");
                throw new Exception("Erreur d'intention");
            }
        } 
        else {
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
        ConfClass conf = new ConfClass();
        TCP_Client client = new TCP_Client();
        try {
            client.start(conf.readIP(), conf.readPort()); // Connexion à ncat en tant que client
            // System.out.println("Client lancé");
        }
        catch (UnknownHostException e) {
            e.printStackTrace();
        }
        catch(IllegalArgumentException e){
            e.printStackTrace();
        }
        catch(SecurityException e){
            e.printStackTrace();
        }
        catch(SocketException e){
            System.out.println("connexion fermée par le server");
        }
        catch (Exception e) {
            e.getCause();
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
