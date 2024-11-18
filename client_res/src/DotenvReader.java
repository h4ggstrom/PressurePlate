import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

public class DotenvReader {

    public static Map<String, String> loadEnv(String filePath) {
        Map<String, String> envVariables = new HashMap<>();

        try (BufferedReader reader = new BufferedReader(new FileReader(filePath))) {
            String line;
            while ((line = reader.readLine()) != null) {
                line = line.trim();
                // Ignorer les lignes vides ou les commentaires
                if (line.isEmpty() || line.startsWith("#")) {
                    continue;
                }
                // Séparer la clé et la valeur
                String[] parts = line.split("=", 2);
                if (parts.length == 2) {
                    String key = parts[0].trim();
                    String value = parts[1].trim();
                    envVariables.put(key, value);
                    // Optionnel : ajouter aux propriétés système
                    System.setProperty(key, value);
                }
            }
        } catch (IOException e) {
            System.err.println("Erreur lors de la lecture du fichier .env : " + e.getMessage());
        }

        return envVariables;
    }

    public static void main(String[] args) {
        String envFilePath = ".env"; // Chemin vers le fichier .env
        Map<String, String> env = loadEnv(envFilePath);

        // Exemple d'utilisation
        System.out.println("DB_HOST: " + env.get("SERVER_PORT"));
        System.out.println("DB_PORT: " + env.get("SERVER_IP"));

        // Ou en utilisant System.getProperty
        System.out.println("DB_USER (via System): " + System.getProperty("SERVER_IP"));
    }
}

