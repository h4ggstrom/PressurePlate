package xmlClass;
public class XMLContent {

    /**
     * Envoie un jeu de données XML à une URL spécifique via une requête POST.
     */
    private String xml_String;

    public  XMLContent(){
    this.xml_String = new String("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
    this.xml_String = this.xml_String + "<content>";
}

    public void addEmployees(String e_id, String e_nom, String e_prenom, String e_poste){
        this.xml_String = this.xml_String + new XMLEmployees(e_id,e_nom,e_prenom,e_poste).GetXMLEmployees();
    }

    public void addFournisseur(String f_id, String f_nom, String f_adresse, String f_num){
        this.xml_String = this.xml_String + new XMLFournisseur(f_id, f_nom, f_adresse, f_num).GetXMLFournisseur();
    }

    public String getXMLContent(){
        this.xml_String = xml_String + "</content>";
        return this.xml_String;
    }
    }



