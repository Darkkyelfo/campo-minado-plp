<?php

require_once 'jogo.php';
require_once 'telaAlerta.php';

class TelaCampo extends GtkWindow {
    var $jogo;//Armazana um obj do tipo jogo.
    var $gladeCampo;//Guarda o caminho para o arquivo .glade.
    var $campoXML;//Carrega o arquivo XML(glade) para criar a tela do campo.
    var $controle;//Serve para chamar apenas uma tela quando o jogador perder
    var $tempo;//Armazena um obj do tipo Time.
    var $tempoMax;//Armzana o tempo máximo de jogado. É um int.
    var $matrizBotao = array(array());//Criar uma matriz de botões
    var $ativarClick=true;
    
    public function __construct($gladeCampo,$jogo,$tempoMax) {
        $this->jogo=$jogo;
        $this->tempoMax=$tempoMax;
        $this->gladeCampo = $gladeCampo;
        $this->campoXML = new GladeXML($gladeCampo);
        //Realiza as conexões entre a tela e os sinais.
        $this->campoXML->get_widget('window1')->connect_simple('destroy', array('Gtk','main_quit'));
        $this->campoXML->get_widget('b_reniciar')->connect_simple('clicked', array($this,'reiniciarGUI'));
        $this->campoXML->get_widget('b_sair')->connect_simple('clicked', array($this,'sair'));
        //Exibe a quantidade de bandeiras disponiveis para uso.
        $this->campoXML->get_widget("labelBandeira")->set_label($this->jogo->numeroBandeiras);
        //coloca o nome do jogador no text box
        $textoNome = $this->campoXML->get_widget('label_jogador');
        $textoNome->set_text($jogo->nomeJogador);
        //Faz com que a IA carregue o campo.
        if($this->jogo->IA !=null){
            $this->jogo->IA->receberCampo($this); 
        }
        $this->tempo = new Time();
    }
    
    public function iniciarGUI(){
        $this->conectarCampo();
        //começa a contar o tempo
        $this->tempo->iniciar($this,$this->tempoMax);
        if($this->jogo->modoDeJogo==3){
             $this->jogadaAutomatica();
         }
        Gtk::main();
    }
    
    //Resposavel por permitir que a IA jogue sozinha
    //Realiza a chamada do método que inicializa a jogada da IA
    //Realiza esse chamada a cada 0,35 segundos
    public function jogadaAutomatica(){
        Gtk::timeout_add(1500,array($this->jogo,"chamarJogadaIA"));
    }

    public function vencer(){
        $this->jogo->getVitoria();//reproduz o som de vitoria
        $telaAlerta = new TelaAlerta($this);
        $telaAlerta->setLabel("Parabéns você venceu!!!");
        $this->controle++;
        if($this->jogo->modoDeJogo==3){
            $this->jogo->turno=true;
        }
        //Desativa a tela
        $this->campoXML->get_widget('window1')->set_sensitive(false);
        $this->tempo->pararContagem();
    }
 
   //chama a janela de derrota
    public function perder(){
        //reproduz o som de uma explosão.
        $this->jogo->getExplosao();
        new TelaAlerta($this);
        $this->controle++;
        $this->jogo->trocarTurnoObg();//impede a ia de jogar quando a partida acaba
        //Desativa a tela
        $this->campoXML->get_widget('window1')->set_sensitive(false);
        $this->tempo->pararContagem();
    }
    
    public function sair(){
        $this->campoXML->get_widget('window1')->destroy();
        $menu = new TelaMenu();
        $menu->iniciarGUI();
    }


    public function reiniciarGUI(){
        $this->tempo->pararContagem();
        $this->tempo = new Time();
        $this->controle=0;
        $armazenarJogo = $this->jogo;
        $this->jogo = new Jogo($armazenarJogo->nomeJogador,$armazenarJogo->campo->linha, $armazenarJogo->campo->coluna, $armazenarJogo->IA,$armazenarJogo->porcentoMinas);
        $this->campoXML->get_widget('window1')->destroy();//destroi a tela
        //cria um novo campo
        $this->campoXML = new GladeXML($this->gladeCampo);
        $this->campoXML->get_widget('window1')->connect_simple('destroy', array('Gtk','main_quit'));
        $this->campoXML->get_widget('b_reniciar')->connect_simple('clicked', array($this,'reiniciarGUI'));
        $this->campoXML->get_widget('b_sair')->connect_simple('clicked', array($this,'sair'));
        $this->campoXML->get_widget("labelBandeira")->set_label($this->jogo->numeroBandeiras);
        //coloca o nome do jogador no text box
        $textoNome = $this->campoXML->get_widget('label_jogador');
        $textoNome->set_text($this->jogo->nomeJogador);
        $this->iniciarGUI();//conecta os botãoes na nova GUI
        $this->campoXML->get_widget('window1')->show_all();//Mostra a GUI;
        $this->jogo->IA->receberCampo($this);
        $this->tempo->iniciar($this, $this->tempoMax);
    }
    //Criar uma matriz de botões e conecta todos os eventos e segnal aos botões
    public function conectarCampo(){
        foreach(range(0,$this->jogo->campo->linha-1) as $i){
            foreach (range(0,$this->jogo->campo->coluna-1) as $j){
                if($j<10){
                    $this->matrizBotao[$i][$j] = $this->campoXML->get_widget("b_".$i.$j);
                }
                else{
                    $this->matrizBotao[$i][$j] = $this->campoXML->get_widget("b_".$i."_".$j);
                }
            }
        }
        foreach(range(0,$this->jogo->campo->linha-1) as $i){
            foreach (range(0,$this->jogo->campo->coluna-1) as $j){
                $this->matrizBotao[$i][$j]->connect_simple('clicked', array($this,'onClickButton'), $i, $j, $this->matrizBotao, $this->jogo);
                $this->matrizBotao[$i][$j]->connect_simple('released', array($this->jogo,'trocarTurno'));//realiza a troca de turnos.
                //Eventos para poder usar o botão direito do mouse
                $this->matrizBotao[$i][$j]->set_events(Gdk::BUTTON_PRESS_MASK);
                //Conexão para poder receber eventos do botão direito do mouse. Colocar bandeiras.
                $this->matrizBotao[$i][$j]->connect('button-press-event', array($this,'colocarBandeira'),$i, $j, $this->matrizBotao, $this->jogo);
            }
        }
    }
    //Quando o usuário clicar com o botão direito ele marca a casa com uma bandeira
    //Para que funcione é nescessário usar um evento e não signal como é no caso da
    //do procedimento onClickButton.
    public function colocarBandeira($widget,$event,$linha, $coluna, $matrizBotao, $jogo){
           //pega a imagem que existe no botão
           $buttonImg= $this->matrizBotao[$linha][$coluna]->get_image();
           //se o botão direito do mouse for clicado
           if($event->button == 3){
               $this->alterarImgBotao($buttonImg,$linha,$coluna);
           }

    }
    //Troca a imagem do botão. 
    public function alterarImgBotao($buttonImg,$linha,$coluna){
               /*
                * Se a imagem do botão for igual a null(não tem imagem) ou 
                * o nome da imagem for fundo troque pela bandeira
                */
               if(($buttonImg == null || $buttonImg->get_name() == "fundo") && ($this->jogo->numeroBandeiras>0)){
                    $this->jogo->numeroBandeiras--;
                    $img = GtkImage::new_from_file("imagens/marcar.png");
                    $img->set_name("bandeira");
                    $this->matrizBotao[$linha][$coluna]->set_image($img);
                    //Caso a casa marcada seja uma bomba ele vai incrementar o número
                    //de bombas achadas.
                    $this->desarmarBomba($linha, $coluna);
                    //Exibe a quantidade de bandeiras disponiveis para uso.
                    $this->campoXML->get_widget("labelBandeira")->set_label($this->jogo->numeroBandeiras);
               }
               //Tira a bandeira.
               else if ($this->ehBandeira($buttonImg)){
                    $this->jogo->numeroBandeiras++;   
                    $img = GtkImage::new_from_file("imagens/planoDeFundo.png");
                    $img->set_name("fundo");
                    $this->matrizBotao[$linha][$coluna]->set_image($img);
                    //Caso a casa a ser desmarcada seja uma bomba, o número de
                    //bombas achadas diminui
                    if ($this->jogo->campo->matriz[$linha][$coluna] ==-1){
                        $this->jogo->bombasAchadas--;
                    }
                    //Exibe a quantidade de bandeiras disponiveis para uso.
                    $this->campoXML->get_widget("labelBandeira")->set_label($this->jogo->numeroBandeiras);
               }
    }
    //Responsavel por incrementar o número de bombas achadas
    private function desarmarBomba($linha,$coluna){
        if ($this->jogo->campo->matriz[$linha][$coluna] ==-1){
            $this->jogo->bombasAchadas++;
                }
        //Se achou todas as bombas encerre o jogo
        if ($this->jogo->bombasAchadas==$this->jogo->numeroDeBombas){
            $this->vencer();
                    }
    }

    //Detecta se na casa clicada tem bandeira.
    public function ehBandeira($buttonImg){
        if($buttonImg != null && $buttonImg->get_name()=="bandeira"){
            return true;
        }
        return false;
    }
    
    /*É responsavel pelo click do botão.
     * Verica se a casa clicada é bomba.
     * Realiza uma chamada recursiva caso a casa clicada seja 0(não é bomba, nem possui bombas ao redor).
     * Chama as telas de vitória e derrota.
     * 
     */ 
    // Esse método é responsavel por definir qual tela deve
    //ser exibida ao termino se clicar em uma bomba
    // Tela de vitoria ou derrota dependendo do tipo de partida e da vez
    //de quem está jogando.
    private function JanelaAposBomba(){
        if ($this->jogo->modoDeJogo!=3) {//Se estiver jogando só ou contra a IA
            if (!($this->jogo->turno)) {
                $this->vencer();
                    } 
            else {
                $this->perder();
                    }
    }
        else{
            $this->perder();
        }
    }
    public function onClickButton($linha, $coluna, $matrizBotao, $jogo) {
        if ($this->ativarClick) {//Desativa a tela para o usuário não click junto com a IA
            $buttonImg = $this->matrizBotao[$linha][$coluna]->get_image();
            if ($this->ehBandeira($buttonImg)) {//caso o usuário click em uma casa com bandeira
                $this->jogo->numeroBandeiras++;//essa bandeira não será perdida
                $this->campoXML->get_widget("labelBandeira")->set_label($this->jogo->numeroBandeiras);
            }
            //vai reniciar a contagem do tempo toda vez que um botão 
            //for clicado
            $this->tempo->zerarTempo();
            $this->matrizBotao[$linha][$coluna]->set_sensitive(false);//desativa o botão clicado
            $campo = $jogo->campo;
            if ($campo->matriz[$linha][$coluna] > 0) {
                //Coloca o número no botão
                $matrizBotao[$linha][$coluna]->set_label($campo->matriz[$linha][$coluna]);
                //Caso o usuário click em uma bomba.
            } else if ($campo->matriz[$linha][$coluna] == -1) {
                $this->acharBombas();
                $buttonImg = $this->matrizBotao[$linha][$coluna]->get_image();
                //Marca as bombas que o usuário achou
                if ($buttonImg != null && $buttonImg->get_name() == "bandeira") {
                    $img = GtkImage::new_from_file("imagens/bomb2.png");
                }
                //Mostra as bombas que o usuário não achou.
                else {
                    $img = GtkImage::new_from_file("imagens/bomb.png");
                }
                //Coloca a imagem na casa onde tem bomba.
                $matrizBotao[$linha][$coluna]->set_image($img);
                if ($this->controle == 0) {
                    $this->JanelaAposBomba();
                }
            } else if ($campo->matriz[$linha][$coluna] == 0) {//Caso clique em uma casa com 0
                $matrizBotao[$linha][$coluna]->set_label("");

                foreach (range($linha - 1, $linha + 1) as $i) {
                    foreach (range($coluna - 1, $coluna + 1) as $j) {

                        if ($campo->verificarIndice($i, $j)) {
                            if (!($matrizBotao[$i][$j]->get_active())) {
                                $matrizBotao[$i][$j]->clicked();//chamada de recurso para todos os botões adjacentes.
                            }
                        }
                    }
                }
            }
        }
    }

    public function acharBombas(){

        $campo= $this->jogo->campo;
        foreach (range(0,$this->jogo->campo->linha-1) as $i){
            foreach (range(0,$this->jogo->campo->coluna-1) as $j){
                if($campo->matriz[$i][$j] == -1){
                    if(!($this->matrizBotao[$i][$j]->get_active())){
                        $this->matrizBotao[$i][$j]->clicked();
                    }
                }
            }
        }
    }
    

}//fim classe
    
