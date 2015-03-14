<?php
if(!defined('MYSTAT_VERSION')){
  throw new Exception('File not exist 404');
}

class dashboard{
  
  protected $context;

  public function __construct($context){
    $this->context = $context;
  }

  public function getName(){
    return $this->context->__('Посещаемость сайта');
  }

  public function getOrderMenu(){
    return 0;
  }

  public function getGroupUUID(){
    return '377da97c-3097-4c0b-9315-125270b9f935';
  }
  
  public function getXML(){
    $data = $this->context->getStat();
    $period = $this->context->getPeriod();
    $ind = Array();
    if(date('Y-m-d',$period['start'])==date('Y-m-d',$period['end'])){
      $uniqhash = Array();
      for($i=0;$i<24;$i++){$ind[$i] = Array('name'=>sprintf($this->context->__('с %d до %d часов'),$i,($i+1)),'timestamp' => strtotime(date('Y-m-d',$period['start']).' '.$i.':0:0'),'uniq'=>0,'count'=>0,'robot'=>0);}
      foreach($data as $d){
        $robot = $uniq = $count = 0;
        if($d['crawler']==true){
          $robot = $ind[(int)date('G',$d['created_at'])]['robot']+1;
          $uniq = $ind[(int)date('G',$d['created_at'])]['uniq'];
          $r = true;
        }else{
          $uniq = $ind[(int)date('G',$d['created_at'])]['uniq']+1;
          $robot = $ind[(int)date('G',$d['created_at'])]['robot'];
          $r = false;
        }
        $count = $ind[(int)date('G',$d['created_at'])]['count']+$d['count'];
        if(in_array($d['ip'],$uniqhash)){
          if($r == false){
            $robot--;
          }else{
            $uniq--;
          }
        }else{
          $uniqhash[] = $d['ip'];
        }
        $ind[(int)date('G',(int)$d['created_at'])]['uniq'] = $uniq;
        $ind[(int)date('G',(int)$d['created_at'])]['count'] = $count;
        $ind[(int)date('G',(int)$d['created_at'])]['robot'] = $robot;
      }
    }else{
      $dayofweek = Array(
        $this->context->__('Воскресенье'),
        $this->context->__('Понедельник'),
        $this->context->__('Вторник'),
        $this->context->__('Среда'),
        $this->context->__('Четверг'),
        $this->context->__('Пятница'),
        $this->context->__('Суббота')
      );
      $datediff = floor($period['end']/(60*60*24)) - floor($period['start']/(60*60*24));
      for($i=0;$i<=$datediff;$i++){
        $ind[date('Y-m-d',strtotime(date('Y-m-d',$period['start']).' 00:00:00 +'.$i.' days'))] = Array(
          'name'=>sprintf('%s, %s',date($this->context->__('d.m.Y'),strtotime(date('Y-m-d',$period['start']).' 00:00:00 +'.$i.' days')),$dayofweek[(int)date('w',strtotime(date('Y-m-d',$period['start']).' 00:00:00 +'.$i.' days'))]),
          'timestamp' => strtotime(date('Y-m-d',$period['start']).' 00:00:00 +'.$i.' days'),
          'uniq'=>0,
          'count'=>0,
          'robot'=>0,
          'holiday' => in_array((int)date('w',strtotime(date('Y-m-d',$period['start']).' 00:00:00 +'.$i.' days')),Array(0,6))?true:false
        );
      }
      $prev = false;
      $uniqhash = Array();
      foreach($data as $d){
        if($prev==false or $prev != date('Y-m-d',$d['created_at'])){
          $prev = date('Y-m-d',$d['created_at']);
          $uniqhash = Array();
        }
        $robot = $uniq = $count = 0;
        $r = false;
        if($d['crawler']==true){
          $robot = $ind[date('Y-m-d',$d['created_at'])]['robot']+1;
          $uniq = $ind[date('Y-m-d',$d['created_at'])]['uniq'];
          $r = true;
        }else{
          $uniq = $ind[date('Y-m-d',$d['created_at'])]['uniq']+1;
          $robot = $ind[date('Y-m-d',$d['created_at'])]['robot'];
          $r = false;
        }
        $count = $ind[date('Y-m-d',$d['created_at'])]['count']+$d['count'];
        if(in_array($d['ip'],$uniqhash)){
          if($r === true){
            $robot--;
          }else{
            $uniq--;
          }
        }else{
          $uniqhash[] = $d['ip'];
        }
        $ind[date('Y-m-d',(int)$d['created_at'])]['uniq'] = $uniq;
        $ind[date('Y-m-d',(int)$d['created_at'])]['count'] = $count;
        $ind[date('Y-m-d',(int)$d['created_at'])]['robot'] = $robot;
      }
      $ind = array_reverse($ind,true);
    }
    $report = Array();
    $indicator = Array();
    foreach($ind as $in){
      $indicator[] = Array(
        'NAME' => $in['name'],
        'TIMESTAMP' => $in['timestamp'],
        'HOLIDAY' => (isset($in['holiday']) and $in['holiday'])?true:false,
        'UNIQ' => $in['uniq'],
        'ROBOT' => $in['robot'],
        'COUNT' => $in['count']
      );
    }
    $report['REPORT'] = Array(
      'TITLE' => $this->getName(),
      'SUBTITLE' => $this->context->__('Краткая информация по различным показателям статистики посещения вашего сайта'),
      'TRANSLATE' => Array(
        'VISITORVIEW' => $this->context->__('Просмотры страниц'),
        'VISITORUNIQ' => $this->context->__('Посетители'),
        'VISITORROBO' => $this->context->__('Роботы и пауки'),
      ),
      'INDICATORS' => Array()
    );
    if(sizeof($indicator)>0){
      $report['REPORT']['INDICATORS']['INDICATOR'] = $indicator;
    }
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    $xml->preserveWhiteSpace = false;
    $this->context->xmlStructureFromArray($xml,$report);
    return $xml->saveXML();
  }


}