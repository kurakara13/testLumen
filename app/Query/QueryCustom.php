<?php

namespace App\Query;


class QueryCustom
{
    static function custom($data, $request){

      $newData = $data;

      if(is_array($request->filter)){
        foreach($request->filter as $item => $values){
          if(is_array($values)){
            foreach ($values as $key => $value) {
              if($key == 'like'){
                $newData = $data->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return stripos(implode($items[$item]),$value) !== false;
                                            }else {
                                              return stripos($items[$item],$value) !== false;
                                            }
                                        });
              }else if($key == '!like'){
                $newData = $data->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return stripos(implode($items[$item]),$value) === false;
                                            }else {
                                              return stripos($items[$item],$value) === false;
                                            }
                                        });
              }else if($key == 'is'){
                $newData = $data->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return implode($items[$item]) == $value;
                                            }else {
                                              return $items[$item] == $value;
                                            }
                                        });
              }else if($key == '!is'){
                $newData = $data->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return implode($items[$item]) != $value;
                                            }else {
                                              return $items[$item] != $value;
                                            }
                                        });
              }
            }
          }
        }
      }


      return $newData;

    }
}
