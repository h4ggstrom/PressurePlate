package xmlClass;
public class XMLFournisseur {

    /**
     * Envoie un jeu de données XML à une URL spécifique via une requête POST.
     */
    private String xml_String;
    public  XMLFournisseur(String f_id, String f_nom, String f_adresse, String f_num){
    this.xml_String = new String("<fournisseur><f_id>"+f_id +"</f_id><f_nom>"+f_nom+"</f_nom><f_adresse>"+f_adresse+"</f_adresse><f_num>"+f_num+"</f_num></fournisseur>");}

    public String GetXMLFournisseur(){
        return this.xml_String;
    }
    }