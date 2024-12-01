
import java.io.*;
import java.net.URL;
import java.nio.file.Paths;
import java.util.*;


public class ConfClass {
    /**
     * @author Alexandre Ledard
     */
    private String ip;
    private int port;
    private String clientID;

    public ConfClass() {
        try {
            URL resource = CSVContent.class.getResource("conf.txt");
            File csvFile = Paths.get(resource.toURI()).toFile();
            Scanner csvReader = new Scanner(csvFile);
            this.clientID = csvReader.nextLine();
            this.ip = csvReader.nextLine();
            this.port = Integer.valueOf(csvReader.nextLine());
            csvReader.close();
        } catch (Exception e) {
            System.out.println("Erreur dans le fichier de configuration, mode default: port=24816 ip:localhost");
            e.printStackTrace();
            this.ip ="localhost";
            this.port = 24816 ;
            this.clientID = "conf";
           
        }

    }

    public String readIP() {
        try {
            URL resource = CSVContent.class.getResource("conf.txt");
            File csvFile = Paths.get(resource.toURI()).toFile();
            Scanner csvReader = new Scanner(csvFile);
            csvReader.nextLine();
            String ip = csvReader.nextLine();
            csvReader.close();
            return ip;
        } catch (Exception e) {
            System.out.println("An error occurred.");
            e.printStackTrace();
            return "";
        }
    }

    public int readPort() {
        try {
            URL resource = CSVContent.class.getResource("conf.txt");
            File csvFile = Paths.get(resource.toURI()).toFile();
            Scanner csvReader = new Scanner(csvFile);
            csvReader.nextLine();
            csvReader.nextLine();
            int port = Integer.valueOf(csvReader.nextLine());
            csvReader.close();
            return port;
        } catch (Exception e) {
            System.out.println("An error occurred.");
            e.printStackTrace();
            return 24816;
        }
    }
    public String getClientID() {
        return this.clientID;
    }
    public String getIp() {
        return this.ip;
    }
    public int getPort() {
        return this.port;
    }
    
}



    