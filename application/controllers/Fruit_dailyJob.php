<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fruit_dailyJob extends CI_Controller {
	public function index() {;
	}


	public function test() {
		print "<pre>";
		$this->db->limit(100);
		$query = $this->db->get('fruit_Test');
		if (!empty($query) && intVal($query->result()) > 0) {
			foreach ($query->result() as $row) {
				print_r($row);
			}
		}

	}

	public function daliyJob_1() {
		ini_set("memory_limit", "512M");

		$fakeDate = date("Y-m-d", strtotime("-1 days"));
		$this->db->where("brd_insertDate", $fakeDate);
		$this->db->delete("fruit_Test");

		foreach (array("ptt", "Dcard", "Mobile01", "UDNnews") as $sourceWeb) {
			$contents = str_replace("\"", "", file_get_contents("./pubCache/{$sourceWeb}_{$fakeDate}.csv"));
			$aTmp = preg_split("/\r?\n/", $contents);
			unset($contents, $aTmp[0]);

			$aSourceWeb = array("PTT", "Dcard", "Mobile01", "UDN", "FB");
			$aScore = array("N/A", "N/A", "N/A", "N/A", "N/A", "N/A", 0, "N/A", 1, "N/A", 2, "N/A", 3, "N/A", 4, "N/A", 5, "N/A", "N/A", "N/A", "N/A");
			$aArticleType = array("5G", "資費", "網速", "收訊", "其它");

			foreach ($aTmp as $key => $tmp_str) {
				$aColumns = explode(",", $tmp_str);
				if (empty(trim($aColumns[3])) || empty(trim($aColumns[1]))) continue;
				if (count($aColumns) > 17) continue;

				$aFeilds[$key]["brd_id"] = uniqid("fruit_", true);
				$aFeilds[$key]["brd_pusher"] = trim($aColumns[0]);
				$aFeilds[$key]["brd_title"] = trim($aColumns[1]);
				$aFeilds[$key]["brd_pushType"] = trim($aColumns[2]);
				$aFeilds[$key]["brd_contents"] = trim($aColumns[3]);
				$aFeilds[$key]["brd_pushTime"] = trim($aColumns[4]);
				$aFeilds[$key]["brd_watchTimes"] = trim($aColumns[5]);
				$aFeilds[$key]["brd_evaluation"] = trim($aColumns[6]);
				$aFeilds[$key]["brd_pusherEvaluation"] = trim($aColumns[7]);
				$aFeilds[$key]["brd_soureUrl"] = trim($aColumns[8]);
				$aFeilds[$key]["brd_sourceWeb"] = $aSourceWeb[array_rand($aSourceWeb)];// crawel_type
				$aFeilds[$key]["brd_fetScore"] = $aScore[array_rand($aScore)];
				$aFeilds[$key]["brd_twnScore"] = $aScore[array_rand($aScore)];
				$aFeilds[$key]["brd_chtScore"] = $aScore[array_rand($aScore)];
				$aFeilds[$key]["brd_gtScore"] = $aScore[array_rand($aScore)];
				$aFeilds[$key]["brd_twsScore"] = $aScore[array_rand($aScore)];
				$aFeilds[$key]["brd_articleType"] = (function() use ($aArticleType) {
					foreach((array) array_rand($aArticleType, intVal(array_rand(array(1, 2, 3)))+1) as $key) $aData[] = $aArticleType[$key];
					return implode(",", $aData);
				})();
				$aFeilds[$key]["brd_insertDate"] = $fakeDate;

				if (preg_match("/^\d+\.\d+$|^http/", $aFeilds[$key]["brd_sourceWeb"])) unset($aFeilds[$key]);
				if (empty($aFeilds[$key]["brd_articleType"])) unset($aFeilds[$key]);
				if (!in_array($aFeilds[$key]["brd_pushType"], array("主文", "推文", "回文"))) unset($aFeilds[$key]);
			}
			if (count((array) $aFeilds) > 0) $this->db->insert_batch("fruit_Test", $aFeilds);
			unset($aTmp, $aFeilds);
		}

		$aFinalCSV = glob("./pubCache/final_*.csv");
		if (count((array) $aFinalCSV) > 0) {
			foreach ($aFinalCSV as $tmp_filePath) {
				$finalCsvDate = (function() use ($tmp_filePath) {
					if (preg_match("/(\d{4}\-\d{2}\-\d{2})/", explode("/", $tmp_filePath)[2], $regs)) return date("Y-m-d",strtotime("-1 days", strtotime($regs[1])));
				})();
				$contents = str_replace("\"", "", file_get_contents($tmp_filePath));
				$aTmp = preg_split("/\r?\n/", $contents);
				unset($contents, $aTmp[0], $aFeilds);

				foreach ($aTmp as $key => $tmp_str) {
					$aColumns = explode(",", $tmp_str);
					if (empty(trim($aColumns[3])) || empty(trim($aColumns[1])) || empty(trim($aColumns[9]))) continue;
					if (count($aColumns) > 17) continue;
					$aFeilds[$key]["brd_id"] = uniqid("fruit_", true);
					$aFeilds[$key]["brd_pusher"] = trim($aColumns[0]) ?? "";
					$aFeilds[$key]["brd_title"] = trim($aColumns[1]) ?? "";
					$aFeilds[$key]["brd_pushType"] = trim($aColumns[2]) ?? "";
					$aFeilds[$key]["brd_contents"] = trim($aColumns[3]) ?? "";
					$aFeilds[$key]["brd_pushTime"] = trim($aColumns[4]) ?? "";
					$aFeilds[$key]["brd_watchTimes"] = trim($aColumns[5]) ?? "";
					$aFeilds[$key]["brd_evaluation"] = trim($aColumns[6]) ?? "";
					$aFeilds[$key]["brd_pusherEvaluation"] = trim($aColumns[7]) ?? "";
					$aFeilds[$key]["brd_soureUrl"] = trim($aColumns[8]) ?? "";
					$aFeilds[$key]["brd_sourceWeb"] = trim($aColumns[9]) ?? "";
					$aFeilds[$key]["brd_fetScore"] = empty(trim($aColumns[10])) ? "N/A": intVal($aColumns[10]);
					$aFeilds[$key]["brd_twnScore"] = empty(trim($aColumns[11])) ? "N/A": intVal($aColumns[11]);
					$aFeilds[$key]["brd_chtScore"] = empty(trim($aColumns[12])) ? "N/A": intVal($aColumns[12]);
					$aFeilds[$key]["brd_gtScore"] = empty(trim($aColumns[13])) ? "N/A": intVal($aColumns[13]);
					$aFeilds[$key]["brd_twsScore"] = empty(trim($aColumns[14])) ? "N/A": intVal($aColumns[14]);
					$aFeilds[$key]["brd_articleType"] = str_replace("\/", ",", $aColumns[15]);
					$aFeilds[$key]["brd_insertDate"] = $finalCsvDate;

					if (preg_match("/^\d+\.\d+$|^http/", $aFeilds[$key]["brd_sourceWeb"])) unset($aFeilds[$key]);
					if (empty($aFeilds[$key]["brd_articleType"])) unset($aFeilds[$key]);
					if (!in_array($aFeilds[$key]["brd_pushType"], array("主文", "推文", "回文"))) unset($aFeilds[$key]);
				}

				//file_put_contents("./application/logs/dump.txt", print_r($aFeilds, TRUE));
				if (count((array) $aFeilds) > 0) {
					$aFeilds = array_values($aFeilds);
					$this->db->where("brd_insertDate", $finalCsvDate);
					$this->db->delete("fruit_Test");
					$this->db->insert_batch("fruit_Test", $aFeilds);
				}
				shell_exec("mv {$tmp_filePath} ./application/cache/");
			}
		}
	}
}
