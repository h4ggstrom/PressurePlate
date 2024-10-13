package client_res.src;
import java.net.*;
import java.io.*;
import java.lang.Exception;
public class TCP_Client {
    private ServerSocket serverSocket;
    private Socket clientSocket;
    private PrintWriter out;
    private BufferedReader in;

    public void start(int port)throws Exception {
        serverSocket = new ServerSocket(port);
        clientSocket = serverSocket.accept();
        out = new PrintWriter(clientSocket.getOutputStream(), true);
        in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));
        out.println("abcde123456");
        String id = in.readLine();
        if(id.equals("id ok")){
             out.println("10ko");
        String volume = in.readLine();
        if(volume.equals("volume ok")){
            out.println("donnee123456");
        }
        else{
            out.println("fichier trop volumineux");
           throw new Exception("Fichier trop volumineux");
        }
        String transfert = in.readLine();
            if (transfert.equals("transfert ok")){
            out.println("succces du transfert, fermeture...");
            System.out.println("Succes transfert");}
            else{
                out.println("transfert error");
                throw new Exception("Echec Transfert");
            }
            }
        else{
            out.println("ID error");
            throw new Exception("ID Error");
        }
        }
    
    public void stop() throws IOException {
        in.close();
        out.close();
        clientSocket.close();
        serverSocket.close();
    }
    public static void main(String[] args) {
        TCP_Client server=new TCP_Client();
        try {
            server.start(6666);
            System.out.println("server lancer");
        } catch (Exception e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
    }
}