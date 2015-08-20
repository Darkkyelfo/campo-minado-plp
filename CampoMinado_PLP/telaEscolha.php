<?php
require_once "telaMenu.php";
require_once "telaCampo.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TelaEscolha extends GtkWindow {
    
    var $escolhaXML;
    
    public function __construct() {
        $this->escolhaXML =new GladeXML('telas/escolhas.glade');
        $this->escolhaXML->get_widget('window1')->connect_simple('destroy', array('Gtk','main_quit'));
        $this->escolhaXML->get_widget('b_jogar')->connect_simple('clicked', array($this,'jogar'));
        $this->escolhaXML->get_widget('b_cancelar')->connect_simple('clicked', array($this,'sair'));
        
    }
    
    public function iniciarGUI(){
        Gtk::main();
    }
    
    public function jogar(){
        //captura o nome do usuário
        $nomeJogador = $this->escolhaXML->get_widget("l_nome")->get_text();
        $gladeCampo;
        $tamanho;
        $porc;
        $tempo;
        $ia;
        //o método get->active retorna se o radio button está ativado ou não.
        //grupo de botões de radio do campo
        if ($this->escolhaXML->get_widget('r_campo5x5')->get_active()){
            $tamanho=5;
            $gladeCampo = "telas/campo5x5.glade";
        }
        else if ($this->escolhaXML->get_widget('r_campo10x10')->get_active()){
            $tamanho=10;
            $gladeCampo = "telas/campo10x10.glade";
        }
        else if ($this->escolhaXML->get_widget('r_campo20x20')->get_active()){
            $tamanho=20;
            $gladeCampo = "telas/campo20x20.glade";
        }
        //acaba aqui o trecho referente aos botões de radio da porcentagem
        //grupo de botões de radio da porcentagem
        if ($this->escolhaXML->get_widget('r_porc15')->get_active()){
            $porc = 0.15;
        }
        else if ($this->escolhaXML->get_widget('r_porc25')->get_active()){
            $porc = 0.25;
        }
        else {
            $porc = 0.5;
        }
        //Acaba aqui o trecho referente aos botões de radio da porcentagem
        //Grupo de botões de radio para o tempo
        if ($this->escolhaXML->get_widget('r_60')->get_active()){
            $tempo = 60;
        }
        else if ($this->escolhaXML->get_widget('r_30')->get_active()){
            $tempo = 30;
        }
        else if ($this->escolhaXML->get_widget('r_15')->get_active()){
            $tempo = 15;
        }
        else{//Caso escolha a opção sem tempo.
            $tempo = null;
        }
        //Acaba aqui o trecho referente aos botões de radio para o tempo.
        
        if($this->escolhaXML->get_widget('r_semIA')->get_active()){
            $ia=null;
        }
        else if($this->escolhaXML->get_widget("r_IA")->get_active()){
            $ia=new IA();
            $ia->nivel=1;
        }
        else if($this->escolhaXML->get_widget("r_IA2")->get_active()){
            $ia=new IA();
            $ia->nivel=2;
        }
        else if($this->escolhaXML->get_widget("r_IA3")->get_active()){
            $ia=new IA();
            $ia->nivel=3;
        }
            //fecha a janela
            $this->escolhaXML->get_widget('window1')->destroy();
            $jogo=new Jogo($nomeJogador,$tamanho,$tamanho,$ia,$porc);
            $gladeCampo = new TelaCampo($gladeCampo,$jogo,$tempo);
            $gladeCampo->iniciarGUI();
    }
    
    public function sair(){
        $escolha = $this->escolhaXML->get_widget('window1');
        $escolha->destroy();
        $telaMenu = new telaMenu();
        $telaMenu->iniciarGUI();
    }
    
}
?>

