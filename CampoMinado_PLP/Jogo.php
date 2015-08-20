<?php
require 'Campo.php';
require_once "IA.php";

class Jogo {
    var $campo;
    var $IA;
    var $porcentoMinas;
    var $nomeJogador;
    var $turno= true;
    var $numeroBandeiras;
    var $numeroDeBombas;
    var $bombasAchadas = 0;
    
    public function __construct($nomeJogador,$linha, $coluna, $IA, $porcentoMinas) {
        $this->porcentoMinas = $porcentoMinas;
        $this->IA = $IA;
        $this->numeroDeBombas= (int)($linha*$coluna*$porcentoMinas);
        $this->numeroBandeiras = $this->numeroDeBombas;
        $this->campo = new Campo($linha, $coluna,  $this->numeroDeBombas);
        $this->nomeJogador=$nomeJogador;
    }
    
    function getExplosao(){
       $BOOM = "c:\Python27\python.exe som/sound.py";
       echo exec($BOOM);
    }
    
    function getVitoria(){
       $win = "c:\Python27\python.exe som/vitoria.py";
       echo exec($win);
    }

    function trocarTurno(){
        //Só realiza a troca se existir uma IA.
        //telaCampo->ativarClick serve para impedir que ocorra
        //uma troca de turnos enquanto a IA joga
        if($this->IA!=null && $this->IA->telaCampo->ativarClick){
        //true sinaliza que é vez do usuário
            if ($this->turno){
                $this->turno=false;
            }
            else{
                $this->turno=true;
            }

            if(!$this->turno){
                if($this->IA->nivel==1){
                    $this->IA->clickNivel1();
                }
                if($this->IA->nivel==2){
                    $this->IA->clickNivel2();
                }
                if($this->IA->nivel==3) {
                    $this->IA->clickNivel3();
                }
            }
        }
    }


    
    
}
?>