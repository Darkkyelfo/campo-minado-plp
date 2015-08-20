<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Time {
    /*
     * Essa classe é responsavel pelo tempo e seus eventos associados.
     *Atualizar a barra de progresso em função desse tempo.
     */
    var $segundos =0;
    var $telaCampo;
    var $barraProgresso;
    var $contar;//serve para sinalizar se o tempo deve ser contado ou não
    var $tempoMax;//guarda o tempo máximo de espera
    
    //inicia a contagem 
    public function iniciar($telaCampo,$tempoMax){
        //Só vai contar o tempo se o $tempoMax for diferente den null.
        //É usado quando o usuário escolhe a opção sem tempo.
        if ($tempoMax != null){
            $this->contar = True;
            $this->tempoMax=$tempoMax;
            $this->telaCampo = $telaCampo;
            $this->barraProgresso=$telaCampo->campoXML->get_widget('barra_tempo');
            Gtk::timeout_add(1000,array($this,"contarTempo"));
        }
    }

    public function contarTempo(){
        //Faz a barra de progresso ser preenchida com o tempo.Leva o tempo escolhido.
        $this->barraProgresso->set_fraction(($this->segundos)*(1.0/$this->tempoMax));
        //Coloca a contagem regressiva na tela de progesso
        $this->barraProgresso->set_text($this->tempoMax-$this->segundos);
        //$this->barraProgresso->set_show_tex(true);
        $this->segundos++;
        //Chama a tela de alerta caso o tempo máximo seja atingindo.
        if ($this->segundos ==$this->tempoMax+1){
            $this->zerarTempo();
            //Faz parar a contagem de tempo
            $this->pararContagem();
            $this->telaCampo->perder();
        }
        return $this->contar;
    }
    //reseta a contagem
    public function zerarTempo(){
        $this->segundos=0;
    }
    public function pararContagem(){
        $this->contar=False;
    }
}


?>

