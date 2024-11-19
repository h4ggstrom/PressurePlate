import java.io.File; // Import the File class
import java.net.URL;
import java.nio.file.Paths;
import java.util.Scanner; // Import the Scanner class to read text files

public class CSVContent {
    /**
     * @author Alexandre Ledard
     * @param String The path of csv file
     *               Permet de convertir en String un fichier csv
     */
    private String csvString;

    public CSVContent(String fileString) {
        this.csvString = "";
        try {
            URL resource = CSVContent.class.getResource(fileString);
            File csvFile = Paths.get(resource.toURI()).toFile();
            Scanner csvReader = new Scanner(csvFile);
            while (csvReader.hasNextLine()) {
                this.csvString = this.csvString + csvReader.nextLine() + "\n";
            }
            csvReader.close();
        } catch (Exception e) {
            System.out.println("An error occurred.");
            e.printStackTrace();
        }
    }

    public String getCSVString() {
        return this.csvString;
    }
}
