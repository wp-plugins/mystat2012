<?php
class BAR{
// Размер изображения
    var $width=600;
    var $height=300;
// Цвет фона (белый)
    var $color_bg='#F2F2F2';
// Цвет задней грани графика (светло-серый)
    var $color_bgWall=array(231,231,231);
// Цвет левой грани графика (серый)
    var $color_lWall=array(212,212,212);
// Цвет сетки (серый, темнее)
    var $color_grid=array(184,184,184);
// Цвет текста (темно-серый)
    var $color_text=array(136,136,136);
// Цвет ошибки (красный)
    var $color_err=array(255,0,0);
// Цвета для столбиков
    var $color_bar=array(
                array(255,128,234),
                array(222,214,0),
                array(128,234,255)
        );
    var $transparent=1;
    var $antialias=true;
    var $smooth=false;
    var $border=3;

    var $img;

function VERSION(){
    return '1.0';
}

function smoothData($e){
    $count=0;
    for($i=0;$i<count($e);$i++){
        if(count($e[$i])>$count)$count=count($e[$i]);
    };
    if($count==0)$count=1;
    for($a=2;$a<$count;$a++){
        for($o=0;$o<count($e)-1;$o++){
            $e[$o][$a]=($e[$o][$a-1]+$e[$o][$a-2]+$e[$o][$a]+$e[$o][$a+1]+$e[$o][$a+2])/5;
        };
    };
    return $e;
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1){
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

function imagebar($x,$y,$w,$h,$dx,$dy,$c1,$c2,$c3){
    if ($dx>0) {
        imagefilledpolygon($this->img,
            Array(
                $x, $y-$h,
                $x+$w, $y-$h,
                $x+$w+$dx, $y-$h-$dy,
                $x+$dx, $y-$dy-$h
            ), 4, $c1);
        imagefilledpolygon($this->img,
            Array(
                $x+$w, $y-$h,
                $x+$w, $y,
                $x+$w+$dx, $y-$dy,
                $x+$w+$dx, $y-$dy-$h
            ), 4, $c3);
        }
    imagefilledrectangle($this->img, $x, $y-$h, $x+$w, $y, $c2);
}

function colorCorrect($col){
    if(!is_array($col)){
        $tmp=array();
        $tmp[0]=hexdec(substr($col,1,2));
        $tmp[1]=hexdec(substr($col,3,2));
        $tmp[2]=hexdec(substr($col,5,2));
        $col=$tmp;
    }else{
        if(!isset($col[0])){$col[0]=0;};
        if(!isset($col[1])){$col[1]=0;};
        if(!isset($col[2])){$col[2]=0;};
    };
    return $col;
}

function makeGraph($DATA){
    if($this->antialias){
        $this->width*=1.4;
        $this->height*=1.4;
    };
    $count=0;
    for($i=0;$i<count($DATA)-1;$i++){
        if(count($DATA[$i])>$count){$count=count($DATA[$i]);};
    };
    if($count==0){return false;break;};
    if($this->smooth){$DATA=$this->smoothData($DATA);};
    $max=0;
    for($i=0;$i<$count;$i++){
        for($j=0;$j<count($DATA)-1;$j++){
            $max=$max<$DATA[$j][$i]?$DATA[$j][$i]:$max;
        };
    };
    $real_max=$max;
    $max=intval($max+($max/10));
    $this->img=imagecreatetruecolor($this->width,$this->height);
    $LW=imagefontwidth($this->antialias?5:2);
    $this->color_bg=$this->colorCorrect($this->color_bg);
    $this->color_bgWall=$this->colorCorrect($this->color_bgWall);
    $this->color_lWall=$this->colorCorrect($this->color_lWall);
    $this->color_grid=$this->colorCorrect($this->color_grid);
    $this->color_text=$this->colorCorrect($this->color_text);
    $this->color_err=$this->colorCorrect($this->color_err);
    $bg[0]=imagecolorallocate($this->img,$this->color_bg[0],$this->color_bg[1],$this->color_bg[2]);
    imagefill($this->img, 0, 0, $bg[0]);
    $bg[1]=imagecolorallocate($this->img,$this->color_bgWall[0],$this->color_bgWall[1],$this->color_bgWall[2]);
    $bg[2]=imagecolorallocate($this->img,$this->color_lWall[0],$this->color_lWall[1],$this->color_lWall[2]);
    $c=imagecolorallocate($this->img,$this->color_grid[0],$this->color_grid[1],$this->color_grid[2]);
    $text=imagecolorallocate($this->img,$this->color_text[0],$this->color_text[1],$this->color_text[2]);
    $err[0]=imagecolorresolvealpha($this->img,($this->color_err[0]+32<=255?$this->color_err[0]+32:255),($this->color_err[1]+32<=255?$this->color_err[1]+32:255),($this->color_err[2]+32<=255?$this->color_err[2]+32:255),150);
    $err[1]=imagecolorresolvealpha($this->img,$this->color_err[0],$this->color_err[1],$this->color_err[2],150);
    $err[2]=imagecolorresolvealpha($this->img,($this->color_err[0]-32>=0?$this->color_err[0]-32:0),($this->color_err[1]-32>=0?$this->color_err[1]-32:0),($this->color_err[2]-32>=0?$this->color_err[2]-32:0),150);
    for($i=0;$i<count($this->color_bar);$i++){
        $this->color_bar[$i]=$this->colorCorrect($this->color_bar[$i]);
        $bar[$i][0]=imagecolorresolvealpha($this->img,($this->color_bar[$i][0]+32<=255?$this->color_bar[$i][0]+32:255),($this->color_bar[$i][1]+32<=255?$this->color_bar[$i][1]+32:255),($this->color_bar[$i][2]+32<=255?$this->color_bar[$i][2]+32:255),$this->transparent);
        $bar[$i][1]=imagecolorresolvealpha($this->img,$this->color_bar[$i][0],$this->color_bar[$i][1],$this->color_bar[$i][2],$this->transparent);
        $bar[$i][2]=imagecolorresolvealpha($this->img,($this->color_bar[$i][0]-32>=0?$this->color_bar[$i][0]-32:0),($this->color_bar[$i][1]-32>=0?$this->color_bar[$i][1]-32:0),($this->color_bar[$i][2]-32>=0?$this->color_bar[$i][2]-32:0),$this->transparent);
    };

    $text_width=strlen($max)*$LW;
    $M=$this->antialias?0:$this->border;$MB=($this->antialias?21:17)+$M;$ML=$text_width+$M+5;
    $DX=(count($DATA)-1)*10;
    $DX1=sqrt(((($this->width-$ML-$M-1-$DX)/$count)*(($this->width-$ML-$M-1-$DX)/$count))*2);
    if($DX>$DX1){$DX=$DX1;};
    if($DX<(count($DATA)-1)*4)$DX=(count($DATA)-1)*4;
    if($this->antialias){
        $DX=$DX*1.4;
    };
    $DY=$DX/2;
    $RW=$this->width-$ML-$M-1-$DX;
    $RH=$this->height-$MB-1-$M-$DY;

    $this->imagelinethick($this->img, $ML, $M+$DY, $ML, $this->height-$MB-1, $c);
    $this->imagelinethick($this->img, $ML, $M+$DY, $ML+$DX, $M, $c);
    $this->imagelinethick($this->img, $ML, $this->height-$MB-1, $ML+$DX, $this->height-$MB-$DY-1, $c);
    $this->imagelinethick($this->img, $ML, $this->height-$MB-1, $this->width-$M-$DX, $this->height-$MB-1, $c);
    $this->imagelinethick($this->img, $this->width-$M-$DX, $this->height-$MB-1, $this->width-$M, $this->height-$MB-$DY-1, $c);

    imagefilledrectangle($this->img, $ML+$DX, $M, $this->width-$M-1, $this->height-$MB-$DY-1, $bg[1]);
    imagerectangle($this->img, $ML+$DX, $M, $this->width-$M-1, $this->height-$MB-$DY-1, $c);
    imagefill($this->img, $ML+1, $this->height/2, $bg[2]);

    for($i=1;$i<count($DATA)-1;$i++){
        imageline($this->img, $ML+$i*intval($DX/(count($DATA)-1)),
            $M+$DY-$i*intval($DY/(count($DATA)-1)),
            $ML+$i*intval($DX/(count($DATA)-1)),
            $this->height-$MB-1-$i*intval($DY/(count($DATA)-1)),
        $c);
        imageline($this->img, $ML+$i*intval($DX/(count($DATA)-1)),
            $this->height-$MB-1-$i*intval($DY/(count($DATA)-1)),
            $this->width-$M-$DX+$i*intval($DX/(count($DATA)-1)),
            $this->height-$MB-1-$i*intval($DY/(count($DATA)-1)),
        $c);
    };

    $X0=$ML+$DX;
    $Y0=$this->height-$MB-1-$DY;

// Вывод изменяемой сетки (вертикальные линии сетки на нижней грани графика
// и вертикальные линии на задней грани графика)
    $last=0;
    for($i=0;$i<$count;$i++){
        if(($X0+$i*($RW/$count))-4>=$last){
            imageline($this->img,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count)-$DX,$Y0+$DY,$c);
            imageline($this->img,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
            $last=$X0+$i*($RW/$count);
        };
    };

// Горизонтальные линии сетки задней и левой граней.
    if($max==0){$max=1;};
    $step=$RW/$count<=$RH?$RW/$count:$RH/$max*$real_max;
    if($step<1){$step=1;};
    $countY=$RH/$step;
    $tmp=imagefontheight($this->antialias?5:2);$last=$Y0;
    for($i=0;$i<=$countY;$i++){
        if(($Y0-$step*$i)+4<=$last){
            imageline($this->img,$X0,$Y0-$step*$i,$X0+$RW,$Y0-$step*$i,$c);
            imageline($this->img,$X0,$Y0-$step*$i,$X0-$DX,$Y0-$step*$i+$DY,$c);
            $last=$Y0-$step*$i;
            if($step*$i>$tmp){
                $tmp=$step*$i+imagefontheight($this->antialias?5:2);
                imageline($this->img,$X0-$DX,$Y0-$step*$i+$DY,
                    $X0-$DX-($ML-$text_width-($this->antialias?2:5)),$Y0-$step*$i+$DY,$text);
            };
        };
    };

// Вывод кубов для всех рядов
    for($j=0;$j<(count($DATA)-1);$j++){
        for($i=0;$i<$count;$i++){
            $this->imagebar($X0+$i*($RW/$count)+4-($j+1)*intval($DX/(count($DATA)-1))-2,
                $Y0+($j+1)*intval($DY/(count($DATA)-1)),
                intval($RW/$count)-3,
                ($RH/$max*$DATA[$j][$i]),
                intval($DX/(count($DATA)-1)),
                intval($DY/(count($DATA)-1)),
                (isset($bar[$j][0])?$bar[$j][0]:$err[0]),
                (isset($bar[$j][1])?$bar[$j][1]:$err[1]),
                (isset($bar[$j][2])?$bar[$j][2]:$err[2])
            );
        };
    };

// Вывод подписей по оси Y
    $tmp=imagefontheight($this->antialias?5:2);
    for($i=1;$i<=$countY;$i++){
        if($step*$i>$tmp){
            $str=intval(($max/$countY)*$i);
            $tmp=$step*$i+imagefontheight($this->antialias?5:2);
            imagestring($this->img,$this->antialias?5:2, $M,
                $Y0+$DY-$step*$i-imagefontheight($this->antialias?5:2)/2,
                $str,$text);
        };
    };

// Вывод подписей по оси X
    $prev=$this->width*2;
    if(strlen($DATA["x"][0])==''){$DATA["x"][0]=1;};
    $twidth=$LW*strlen($DATA["x"][0])+6;
    $i=$X0+$RW-$DX;
    while($i>$X0-$DX){
        if($prev-$twidth>$i){
            $drawx=$i+1-($RW/$count)/2;
            if($drawx>$X0-$DX){
                $str=$DATA["x"][round(($i-$X0+$DX)/($RW/$count))-1];
                if(strlen($str)==''){$str=round(($i-$X0+$DX)/($RW/$count));};
                imageline($this->img,$drawx,$Y0+$DY,$i+1-($RW/$count)/2,$Y0+$DY+5,$text);
                imagestring($this->img,$this->antialias?5:2,($drawx+1+((strlen($str)*$LW)/2)<=$this->width?$drawx+1-(strlen($str)*$LW)/2:$this->width-(strlen($str)*$LW)) ,$Y0+$DY+7,$str,$text);
                if($drawx+1+((strlen($str)*$LW)/2)>$this->width){$twidth=$LW*strlen($str)+6+(($this->width-($this->width-(strlen($str)*$LW)))-($this->width-($drawx+1-(strlen($str)*$LW)/2)));}else{$twidth=$LW*strlen($str)+6;};
            };
            $prev=$i;

        };
        $i-=$RW/$count;
    };
    if($this->antialias){
        $this->width/=1.4;
        $this->height/=1.4;
        imageantialias($this->img,true);
        $tmp=imagecreatetruecolor($this->width,$this->height);
        imagefill($tmp, 0, 0, $bg[0]);
        imagecopyresampled($tmp,$this->img,$this->border,$this->border,0,0,$this->width-$this->border*2,$this->height-$this->border*2,$this->width*1.4,$this->height*1.4);
        $this->img=$tmp;
    };
}

function showGraph($type='png'){
    if(isset($this->img)){
        imageantialias($this->img,true);
        if($type=='png'){
            header("Content-Type: image/png");
            ImagePNG($this->img);
        }elseif($type=='gif'){
            header("Content-type: image/gif");
            imagegif($this->img);
        }elseif($type=='jpeg'){
            header("Content-type: image/jpeg");
            imagejpeg($this->img,"",0.8);
        }elseif($type=='wbmp'){
            header("Content-type: image/vnd.wap.wbmp");
            imagewbmp($this->img);
        }elseif($type=='xbm'){
            header("Content-type: image/xbm");
            imagexbm($this->img);
        } else {
            die("No support image format!");
        };
    };
}

function returnGraph(){
    return $this->img;
}

function freeGraph(){
    if(isset($this->img)){
        imagedestroy($this->img);
    };
}
};
?>