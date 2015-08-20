<?php

class Campo {

    var $linha;
    var $coluna;
    var $matriz = array(array());
    var $porcentoBombas;

    public function __construct($linha, $coluna, $qtBombas) {
        $this->linha = $linha;
        $this->coluna = $coluna;
        $this->porcentoBombas = $qtBombas;
        foreach(range(0,$this->linha-1) as $_linha){
            foreach(range(0,$this->coluna-1) as $_coluna){
                $this->matriz[$_linha][$_coluna] = 0;
            }   
        }
        
        $this->inserirBomba($qtBombas);
        $this->contarBombas();
    }
    
    public function contarBombas(){
        foreach(range(0,$this->linha-1) as $i) {
            foreach(range(0,$this->coluna-1) as $j) {
                if($this->matriz[$i][$j]!=-1){
                    $this->matriz[$i][$j] = (int)$this->contarMinaVisinho($i, $j);
                }     
            }
        }    
    }
    public function inserirBomba($quantidade){
        
        for($i=0;$i<$quantidade;$i++){
            $l = (int)(rand(0, $this->linha-1));
            $c = (int)(rand(0, $this->coluna-1));
        
            
            if($this->matriz[$l][$c] != -1){
                $this->matriz[$l][$c] = -1;
            }else{
                $i--;
            }
        }
    }
    public function mostrar(){
        for($i=0;$i<$this->linha;$i++){
            echo '<table border="1">'."<tr bgcolor=gray height=30 width=30>";
            for($j=0;$j<$this->coluna;$j++){
                echo "<td height=30 width=30>".$this->matriz[$i][$j]."</td >";
            }
            echo "</tr>"."</table>";
        }
    
    }
    
    public function verificarIndice($linha, $coluna){
        return ($linha< $this-> linha and $coluna < $this->coluna) and ($linha > -1 and $coluna > -1);
    }
    
    public function contarMinaVisinho($linha, $coluna){
        $cont = 0;
        foreach(range($linha-1,$linha+1)as $i){
            foreach(range($coluna-1,$coluna+1)as $j){
                if($this->verificarIndice($i, $j)){
                    if($this->matriz[$i][$j] == -1){
                       $cont++;
                    }
                }
            }
        }
        return $cont;
    }  
    
}//fim class Campo

?>

 
 
