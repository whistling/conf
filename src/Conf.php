<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/11
 * Time: 22:32
 */

namespace Ants\Conf;


class Conf
{

    /**
     * 写文件
     * 支持数组写入
     */
    public function write($data, $fileName)
    {
        $confPath = storage_path('conf');
        $filePath = $confPath . '/' . $fileName;

        /** 文件存在，先获取内容，是数组就合并，并清空重写 ，是字符串直接追加**/
        if (file_exists($filePath)) {

            $rawData = self::get(array_first(explode('.', $fileName)));
            if (is_array($data)) {
                file_put_contents($filePath, '');
                $data = array_merge($rawData, $data);
            } else if (is_string($data)) {
                file_put_contents($filePath, $data . PHP_EOL, FILE_APPEND);
                return;
            }
        }

        /** 文件不存在，直接写**/
        if (is_array($data)) {
            $data = array_dot($data);
            foreach ($data as $key => $val) {
                $content = $key . '=' . $val . PHP_EOL;
                file_put_contents($filePath, $content, FILE_APPEND);
            }

        } else if (is_string($data)) {
            file_put_contents($filePath, $data . PHP_EOL, FILE_APPEND);
        }

    }


    /**
     * 读文件
     * 支持  user,user.username 格式
     *
     */
    public function get($key)
    {

        /** 文件名和获取配置项的key **/
        if (count(explode('.', $key)) > 1) {
            $confName = substr($key, 0, strpos($key, '.'));
            $confKey = substr($key, strpos($key, '.') + 1);
        } else {
            $confName = $key;
            $confKey = '';
        }

        $confPath = storage_path('conf');
        $filePath = $confPath . '/' . $confName . '.conf';

        if (file_exists($filePath)) {

            $dataAll = file_get_contents($filePath);
            $dataArray = array_values(array_filter(explode(PHP_EOL, $dataAll)));

            /** 获取内容转换成数组格式 **/
            $tmp = [];
            foreach ($dataArray as $k => $v) {
                $_data = explode('=', $v);
                /** 过滤注释 **/
                if (substr($v, 0, 1) == '#') {
                    continue;
                }
                /** 数组格式 **/
                if (count($_data) == 2) {
                    $tmp[array_first($_data)] = array_last($_data);
                }

                /** 字符串格式 **/
                if (count($_data) == 1) {
                    $tmp[] = $v;
                }
            }

            /** 「点」格式转换成数组格式 **/
            $returnArray = [];
            foreach ($tmp as $n => $val) {
                array_set($returnArray, $n, $val);
            }

            /** 返回配置 **/
            if (!empty($confKey)) {
                /** 返回指定配置 **/
                return data_get($returnArray, $confKey);
            } else {
                /** 返回所有配置  **/
                return $returnArray;
            }

        } else {
            return [];
        }

    }

}