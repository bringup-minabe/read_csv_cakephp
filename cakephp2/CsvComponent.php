<?php
/**
 * Csv読み込みComponent
 *
 */
App::uses('Component', 'Controller');

class CsvComponent extends Component {

    public $Error = false;
    public $CountRows = 0;

/**
 * checkCsv
 * Csv形式チェック
 *
 * @param array $file
 * @return bool
 */
    public function checkCsv($file = null){
        if (empty($file)) {
            return false;
        }
        if (isset($file['tmp_name'])){
            $tmp = $file['tmp_name'];
            if (is_uploaded_file($tmp)) {
                setlocale(LC_ALL, 'ja_JP.UTF-8');
                $file_name = basename($file['name']);
                //拡張子取得
                $info = new SplFileInfo($file_name);
                if ($info->getExtension() === 'csv') {
                    return true;
                }
            }
        }
        return false;
    }

/**
 * readCsv
 * Csv読み込み
 *
 * @param array $file
 * @param int $param['columns'] 列数指定
 * @return array
 */
    public function readCsv($file = null, $param = array()){
        
        if (empty($file)) {
            return null;
        }

        $tmp_data = array();
        $data = array();

        if (isset($file['tmp_name'])){
            $tmp = $file['tmp_name'];
            if (is_uploaded_file($tmp)) {

                //ファイル名処理
                setlocale(LC_ALL, 'ja_JP.UTF-8');
                $basename = basename($file['name']);
                $this->FileName = mb_convert_encoding($basename, 'UTF-8', 'auto');

                //CSV読み込み
                mb_language('Japanese');
                $sfb = new SplFileObject($tmp);
                $sfb->setFlags(SplFileObject::READ_CSV);
                foreach ($sfb as $key => $line) {
                    //空行対応
                    if (empty($line[0]) && count($line) == 1) {
                        continue;
                    }
                    foreach ($line as $value) {
                        //データを配列へ
                        $data[$key][] = mb_convert_encoding($value, 'UTF-8', 'auto');

                        //列数指定があった場合にチェック
                        if (isset($param['columns'])) {
                            if ($param['columns'] != count($line)) {
                                $this->Error = true;
                            }
                        }
                    }
                    $this->CountRows++;
                }
            }
        }

        return $data;
    }
}