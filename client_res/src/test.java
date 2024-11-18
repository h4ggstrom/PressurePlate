import xmlClass.XMLContent;
import xmlClass.XMLEmployees;
import xmlClass.XMLFournisseur;
public class test {
    public static void main(String[] args) {
        XMLContent xml_content = new XMLContent();
                 xml_content.addEmployees("e_00000006","Lachaise","Jean","Caissier");
                 xml_content.addFournisseur("fourni000001", "XML Company", "5 adresse de là bas", "0134225614");
                System.out.println(xml_content.getXMLContent());
    }
}
