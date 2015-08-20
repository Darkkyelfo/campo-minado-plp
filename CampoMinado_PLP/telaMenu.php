<?php
require_once "telaCampo.php";
require_once "telaEscolha.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class TelaMenu extends GtkWindow{
    var $menuXML;
    var $telaAnterior;
    var $campo;
    
    public function __construct() {
        $this->menuXML =new GladeXML('telas/Menu.glade');
        $this->menuXML->get_widget('window1')->connect_simple('destroy', array('Gtk','main_quit'));
        $this->menuXML->get_widget('b_novo')->connect_simple('clicked', array($this,'novoJogo'));
        $this->menuXML->get_widget('b_sair')->connect_simple('clicked', array($this,'sair'));
        $this->menuXML->get_widget("entry1")->set_sensitive(false);//desativa os eventos do textBox
    }
    
    public function iniciarGUI(){
        Gtk::main();
    }
    
    public function novoJogo(){
   
        $menu = $this->menuXML->get_widget('window1');
        $menu->destroy();
        $escolha = new TelaEscolha();
        $escolha->iniciarGUI();
        
    }
    
    public function sair(){
        Gtk::main_quit();
    }
    
}
    
?>

