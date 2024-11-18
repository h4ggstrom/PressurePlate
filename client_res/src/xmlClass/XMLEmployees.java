package xmlClass;
public class XMLEmployees {

    /**
     * Envoie un jeu de données XML à une URL spécifique via une requête POST.
     */
    private String xml_String;
    public  XMLEmployees(String e_id, String e_nom, String e_prenom, String e_poste){
    this.xml_String = new String("<employees><e_id>"+e_id +"</e_id><e_nom>"+e_nom+"</e_nom><e_prenom>"+e_prenom+"</e_prenom><e_poste>"+e_poste+"</e_poste></employees>");}

    public String GetXMLEmployees(){
        return this.xml_String;
    }
    }



