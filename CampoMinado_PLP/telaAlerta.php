<?php
include_once 'telaCampo.php';
class TelaAlerta extends GtkWindow{
    var $alerta;
    var $telaAnterior;
    
    public function __construct($telaAnterior) {
        $this->telaAnterior = $telaAnterior;
        $this->alerta = new GladeXML('telas/alerta.glade');
        $rotulo = $this->alerta->get_widget('label');
        $rotulo->set_label("Voce Perdeu!!");
        $this->alerta->get_widget('jogar')->connect_simple('clicked', array($this,'jogar'));
        $this->alerta->get_widget('sair')->connect_simple('clicked', array($this,'sair'));
    }
    //serve para fechar a janela sem fechar o jogo
    public function fechar(){
        $d = $this->alerta->get_widget('dialog');
        $d->destroy();
    }
    //quando clicar no botão "sair" ele irá voltar para o menu
    public function sair(){
        //destroi o campo
        $this->telaAnterior->campoXML->get_widget('window1')->destroy();
        $this->fechar();
        $menu = new TelaMenu();
        $menu->iniciarGUI();
    }
    public function jogar(){
           $this->fechar();
           $this->telaAnterior->reiniciarGUI();
    }
    
    public function setLabel($label){
        $rotulo = $this->alerta->get_widget('label');
        $rotulo->set_label($label);
    }
   
}


    

    

