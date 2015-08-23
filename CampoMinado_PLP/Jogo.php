<?php
require 'Campo.php';
require_once "IA.php";

class Jogo {
    var $campo;
    var $IA;
    var $porcentoMinas;
    var $nomeJogador;
    var $turno= true;//true é a vez do jogador, false a da IA
    var $numeroBandeiras;
    var $numeroDeBombas;
    var $bombasAchadas = 0;
    var $modoDeJogo =2;//1 - jogador sozinho, 2 - jogador contra IA, 3 - só a IA
    
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
    //Esse método é responsavel por ativar a jogada da IA
    function chamarJogadaIA(){
        if($this->turno==false){
            print("entrei");
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
        return true;
    }
    function trocarTurno(){
        //Só realiza a troca se existir uma IA.
        //telaCampo->ativarClick serve para impedir que ocorra
        //uma troca de turnos enquanto a IA joga
        if($this->modoDeJogo==2 && $this->IA->telaCampo->ativarClick){
        //true sinaliza que é vez do usuário
            if ($this->turno){
                $this->turno=false;
            }
            else{
                $this->turno=true;
            }
            $this->chamarJogadaIA();//Chama a jogada da IA, caso seja sua vez
        }
        }
    }


    
    
?>