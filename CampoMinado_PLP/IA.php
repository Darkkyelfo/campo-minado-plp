<?php
require_once "telaCampo.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Modifiquei esse comentario ^^
class IA{
    var $nome = "IA";
    var $telaCampo;
    var $matrizIA = array(array());
    var $cont=0;//contador usado para atrasar a jogada
    var $linhaEscolhida;//linha a ser clicada
    var $colunaEscolhida;//coluna a ser clicada
    var $nivel;//Guarda o nível da IA
    //Recebe a telaCampo para poder acessar os atributos:Campo matrizDebotoes
    //e também permitir a IA clicar no campo.
    public function receberCampo($telaCampo) {
        $this->telaCampo=$telaCampo;
        foreach (range(0, $this->telaCampo->jogo->campo->linha-1) as $i){
            foreach (range(0, $this->telaCampo->jogo->campo->coluna-1) as $j){
              $this->matrizIA[$i][$j] = 0;
          }
        }
    }
    
    public function imprimirMatriz(){
        foreach (range(0,$this->telaCampo->jogo->campo->linha-1) as $i){
            echo "\n";
            foreach (range(0, $this->telaCampo->jogo->campo->coluna-1) as $j){
                echo $this->matrizIA[$i][$j]." ";
            }
    }
    }
    //Faz com que AI receba o campo que está sendo exibido na tela
    private function AtualizarMatrizIA(){
        
        foreach (range(0,$this->telaCampo->jogo->campo->linha-1) as $i){
            foreach (range(0, $this->telaCampo->jogo->campo->coluna-1) as $j){
                if($this->telaCampo->matrizBotao[$i][$j]->get_active()){//verifica se o botão esta clicado.(clicado=1,!clicado=0)
                    $this->matrizIA[$i][$j] = $this->telaCampo->jogo->campo->matriz[$i][$j];
                }else{
                    $this->matrizIA[$i][$j] = -2;//elemento desconhecido
                }
            }          
        }
    }

    //Verifica se um número estiver tocando o mesmo número de quadrados, então todos esses quadrados são minas.
    private function padraoSimples(){
        foreach (range(0, $this->telaCampo->jogo->campo->linha-1)as $i){
            foreach (range(0, $this->telaCampo->jogo->campo->coluna-1) as $j){
                if($this->telaCampo->matrizBotao[$i][$j]->get_active()){//verifica se o botão está clicado.(clicado=1,!clicado=0)
                    if($this->telaCampo->jogo->campo->matriz[$i][$j]==$this->quantCasasDesconhecidas($i, $j)){
                        $this->marcarBombas($i, $j);
                    }
                }
            }
        }
    }
    

    //Marca os vizinhos de uma casa clicada como bombas.
    private function marcarBombas($i, $j){
        foreach (range($i - 1, $i + 1) as $l) {
            foreach (range($j - 1, $j + 1) as $c) {
                if ($this->telaCampo->jogo->campo->verificarIndice($l, $c)) {
                    if ($this->telaCampo->matrizBotao[$l][$c]->get_active() == false && $this->matrizIA[$l][$c] != "L") {
                        $this->matrizIA[$l][$c] = -1; //o elemento é uma bomba
                    }
                }
            }
        }
    }
    
    //Detecta se só existem casas com bombas a serem clicadas.
    //Obriga a IA a clicar e perder a partida.
    private function soTemBombas(){
        $qtCasasLivres=0;
        $qtBombas=0;
        $linha = $this->telaCampo->jogo->campo->linha;
        $coluna = $this->telaCampo->jogo->campo->coluna;
        foreach(range(0,$linha-1)as $i){
            foreach (range(0, $coluna-1) as $j){
                if(!$this->telaCampo->matrizBotao[$i][$j]->get_active()){
                    $qtCasasLivres++;
                }
                if($this->matrizIA[$i][$j]==-1){
                    $qtBombas++;
                }
            }
        }
        if($qtBombas==$qtCasasLivres){
            return true;
        }
        return false;
        
    }
    
    //Acha as casas que não tem bombas
    public function acharCasasLivres() {
        foreach (range(0, $this->telaCampo->jogo->campo->linha - 1) as $i) {
            foreach (range(0, $this->telaCampo->jogo->campo->coluna - 1) as $j) {
                if ($this->telaCampo->matrizBotao[$i][$j]->get_active()) {
                    if ($this->quantBombas($i, $j) == $this->matrizIA[$i][$j]) {
                        $this->marcarCasasLivres($i, $j);
                    }
                }
            }
        }
    }
    
    //Marca as casas clicaveis 
    private function marcarCasasLivres($i, $j) {

        foreach (range($i - 1, $i + 1) as $l) {
            foreach (range($j - 1, $j + 1) as $c) {
                if ($this->telaCampo->jogo->campo->verificarIndice($l, $c)) {
                    if ($this->telaCampo->matrizBotao[$l][$c]->get_active()== false && $this->matrizIA[$l][$c] != -1 && $this->matrizIA[$l][$c] != "L") {
                        $this->matrizIA[$l][$c] = "L"; //o elemento não é bomba.
                    }
                }
            }
        }
    }
    //retorna o numero de bombas vizinhas marcadas
    private function quantBombas($i, $j) {
        $numBombas = 0;
        foreach (range($i - 1, $i + 1) as $l) {
            foreach (range($j - 1, $j + 1) as $c) {
                if ($this->telaCampo->jogo->campo->verificarIndice($l, $c)) {
                    if ($this->telaCampo->matrizBotao[$l][$c]->get_active()== false  && $this->matrizIA[$l][$c] == -1) {
                        $numBombas++;
                    }
                }
            }
        }
        return $numBombas;
    }
    //retorna o numero de visinhos não clicados
    private function quantCasasDesconhecidas($i, $j){
        $numCasas=0;
      
        foreach (range($i-1, $i+1) as $l){
            foreach (range($j-1, $j+1) as $c){
                if($this->telaCampo->jogo->campo->verificarIndice($l, $c)){
                    if($this->telaCampo->matrizBotao[$l][$c]->get_active()==false && $this->matrizIA[$l][$c] != "L"){
                        $numCasas++;
                    }
                }
            }
        }
        return $numCasas;
    }
    //Realiza a copia da matriz da IA
    private function copiarMatriz(){
        $matrizCopia=array(array());
        foreach (range(0, $this->telaCampo->jogo->campo->linha - 1) as $i) {
                foreach (range(0, $this->telaCampo->jogo->campo->coluna - 1) as $j) {
                    $matrizCopia[$i][$j]=$this->matrizIA[$i][$j];
                }
    }
        return $matrizCopia;
    }
    //Copara a matriz copiada e verifica se ela é igual a matriz da IA
    private function compararMatriz($matrizCopia){
        foreach (range(0, $this->telaCampo->jogo->campo->linha - 1) as $i) {
                foreach (range(0, $this->telaCampo->jogo->campo->coluna - 1) as $j) {
                    if($matrizCopia[$i][$j] != $this->matrizIA[$i][$j]){
                        return false;
                    }
                }
    }
        return true;
    }
    
    public function probabilidade() {
        $bool = true;
        while ($bool) {
            $matrizCopia = $this->copiarMatriz();
            $numBombas = 0;
            foreach (range(0, $this->telaCampo->jogo->campo->linha - 1) as $i) {
                foreach (range(0, $this->telaCampo->jogo->campo->coluna - 1) as $j) {
                    if ($this->telaCampo->matrizBotao[$i][$j]->get_active()) {
                        $numBombas = $this->matrizIA[$i][$j]; //total de bombas na vizinhança.

                        $numBombas = $numBombas - $this->quantBombas($i, $j); //subtrai as bombas marcadas do total.

                        $casas = $this->quantCasasDesconhecidas($i, $j) - $this->quantBombas($i, $j);

                        if ($casas == 0 && $this->soTemBombas()==false) {
                            $this->marcarBombas($i, $j);
                        } else {
                            $prob = $numBombas / $casas;
                            $prob = $prob * 100;

                            foreach (range($i - 1, $i + 1)as $l) {
                                foreach (range($j - 1, $j + 1) as $c) {
                                    if ($this->telaCampo->jogo->campo->verificarIndice($l, $c) && $this->telaCampo->matrizBotao[$l][$c]->get_active() == false) {
                                        if ($prob == 100 && $this->matrizIA[$l][$c] != -1 && $this->matrizIA[$l][$c] != "L") {
                                            $this->matrizIA[$l][$c] = -1;
                                        } else if ($prob > 0 && $this->matrizIA[$l][$c] != "L" && $this->matrizIA[$l][$c] != -1) {
                                            if ($this->matrizIA[$l][$c] < $prob) {
                                                $this->matrizIA[$l][$c] = ((int) $prob) . "%";
                                            }
                                        } else if ($prob == 0 && $this->matrizIA[$l][$c] != -1 && $this->matrizIA[$l][$c] != "L") {
                                            $this->matrizIA[$l][$c] = "L";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($this->compararMatriz($matrizCopia)){
                $bool=false;
            }
        }
    }
    //Método para inteligência da IA nível 1
    public function clickNivel1(){
        $bool = true;
        $linha = mt_rand(0,$this->telaCampo->jogo->campo->linha-1);
        $coluna = mt_rand(0,$this->telaCampo->jogo->campo->coluna-1);
        //Verifica se o botão está clicado.
        $clicado = $this->telaCampo->matrizBotao[$linha][$coluna]->get_active();
        //Enquanto o índice não for válidao ou o botão estiver clicado.
        //Ele buscará novos valores de indice
        $this->AtualizarMatrizIA();
        $this->padraoSimples();
        $this->imprimirMatriz();
        while ($bool){
            //Caso o botão esteja clicaco ele realizará o loop navamente até achar
            //um botão que não esteja clicado
            while($clicado){
                $linha = mt_rand(0,$this->telaCampo->jogo->campo->linha-1);
                $coluna = mt_rand(0,$this->telaCampo->jogo->campo->coluna-1);
                $clicado = $this->telaCampo->matrizBotao[$linha][$coluna]->get_active();
            }
            //Caso o botão achado não seja uma bomba ele saíra do loop
            if($this->matrizIA[$linha][$coluna]!=-1){
                $bool=false;
            }
            else{
                $clicado = true;
            }
            //Caso só existam bombas para clicar. Ela para o loop
            //e clicla em qualquer casa,pois,não existe possibilidade de 
            //vitoria
           if($this->soTemBombas()){
                $bool = false;
            }
            $this->chamarClick($linha, $coluna);
        }

    }
    //Método para inteligência da IA nível 2
    public function clickNivel2(){
        $linha=null;
        $coluna=null;
        $entrou=false;
        $this->AtualizarMatrizIA();
        $this->padraoSimples();
        $this->acharCasasLivres();
      //  $this->imprimirMatriz();
        foreach (range(0, $this->telaCampo->jogo->campo->linha - 1) as $i) {
            foreach (range(0, $this->telaCampo->jogo->campo->coluna - 1) as $j) {
                if ($this->matrizIA[$i][$j] === "L" && $entrou==false) {
                    $linha = $i;
                    $coluna = $j;
                    $entrou=true;
                }
            }
        }
        if($entrou==false){
            $this->clickNivel1();
        }else{
            $this->chamarClick($linha, $coluna);
        }
    }
    //Método para inteligência da IA nível 3
    public function clickNivel3(){
        $entrou=false;
        $probMin=100;
        $linha=Null;
        $coluna=Null;
        $linhaProb=Null;
        $colunaProb=Null;
        $this->AtualizarMatrizIA();
        $this->probabilidade();
      //  $this->imprimirMatriz();
        foreach (range(0, $this->telaCampo->jogo->campo->linha - 1) as $i) {
            foreach (range(0, $this->telaCampo->jogo->campo->coluna - 1) as $j) {
                if ($this->matrizIA[$i][$j] === "L" && $entrou==false) {//Encontra casas livres(sem bombas)
                    $linha = $i;
                    $coluna = $j;
                    $entrou=true;
                }
                else if ($this->matrizIA[$i][$j]>0 && gettype($this->matrizIA[$i][$j])=="string"){
                    if($this->matrizIA[$i][$j]<=$probMin){//Encontra a menor probalidade.
                        $probMin=$this->matrizIA[$i][$j];
                        $linhaProb=$i;
                        $colunaProb=$j;
                    }
                }
            }
        }
        if($entrou==false && $linhaProb!=null){//click por probalidade
            $this->chamarClick($linhaProb,$colunaProb);
        }
        else if($entrou==true){//click em certeza de casa livre
            $this->chamarClick($linha,$coluna);
        }
        else{//click aleatorio(chute)
            $this->clickNivel1();
        }
    }

    //Atrasa a jogada da IA e marca o local onde ela vai clicar
    private function chamarClick($linha,$coluna){
       //Marca a casa onde a IA vai jogar
        $img = GtkImage::new_from_file("imagens/decepticon.png");
        $this->telaCampo->matrizBotao[$linha][$coluna]->set_image($img);
        $this->linhaEscolhida=$linha;
        $this->colunaEscolhida=$coluna;
        $this->telaCampo->ativarClick=false;//Desativa a tela para o usuário não click junto com a IA
        $this->iniciarAtraso();//Atrasa a jogada da IA para que o usuário possa ver onde ela vai jogar
    }
    
    public function iniciarAtraso(){
        Gtk::timeout_add(1000,array($this,"atrasarJogada"));
    }
    //Atrasa a jogada da IA em 1 segundo para que o usuário possa ver onde ela clicou
    public function atrasarJogada(){
        $continuar = true;
        $this->cont++;
        
        if($this->cont==1){
            $continuar=false;
            $this->cont=0;
            $this->telaCampo->ativarClick=true;//Ativa o click novamente
            $this->telaCampo->matrizBotao[$this->linhaEscolhida][$this->colunaEscolhida]->clicked();//IA click
            $this->telaCampo->jogo->turno=true;//Troca para o turno do usuário
        }
        
        return $continuar;
    }
    
    
}

