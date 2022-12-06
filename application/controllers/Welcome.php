<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Welcome extends CI_Controller {
	public $scoreISP = "brd_fetScore";
	//public $scoreISP = "brd_chtScore";
	//public $scoreISP = "brd_twnScore";
	//public $scoreISP = "brd_gtScore";
	//public $scoreISP = "brd_twsScore";

	public function index() {
		$this->load->view('main_header_v');
		$this->load->view('welcome_message_v');
		$this->load->view('main_footer_v');
	}

	public function getTypeLight() {
		$this->scoreISP = $this->uri->segments[3];
		$data["result"] = FALSE;
		foreach (array("5G", "資費", "收訊", "網速", "其他") as $tmp_articleType) {
			$this->db->select("round(avg({$this->scoreISP})) as brd_ispScore");
			// $this->db->where("brd_insertDate", date("Y-m-d", strtotime("-1 days")));
			$this->db->where("brd_insertDate", date("Y-m-d"));
			//$this->db->where("brd_insertDate", "2021-12-20");
			$this->db->where("{$this->scoreISP} !=", "None");
			$this->db->where("{$this->scoreISP} !=", "");
			$this->db->like("brd_articleType", $tmp_articleType);
			$query = $this->db->get("fruit_Test");
			
			if (!empty($query) && $query->num_rows() > 0) {
				$data["result"] = TRUE;
				foreach ($query->result() as $row) {
					$tmp_light = "greenLight";
					if (intVal($row->brd_ispScore) < 3) $tmp_light = "redLight";
					if (intVal($row->brd_ispScore) === 3) $tmp_light = "yellowLight";
					if (!is_numeric($row->brd_ispScore)) $tmp_light = "greyLight";
					$data["light"][] = $tmp_light;
				}
			}
		}
		print json_encode($data);
		file_put_contents("./application/logs/dump_getTypeLight_db.txt", print_r($this->db, TRUE));
		file_put_contents("./application/logs/dump_getTypeLight_data.txt", print_r($data, TRUE));
	}

	public function getLineChartData() {
		$this->scoreISP = $this->uri->segments[3];
		$data["result"] = FALSE;
		$this->db->select("brd_insertDate");
		$this->db->group_by("brd_insertDate");
		$this->db->order_by("brd_insertDate");
		$query = $this->db->get("fruit_Test");
		if (!empty($query) && $query->num_rows() > 0) {
			foreach ($query->result() as $key => $row) {
				$data["areaChartData"]["labels"][$key] = $row->brd_insertDate;
			}
		}
		foreach (array("5G", "資費", "收訊", "網速", "其他") as $datasetsKey => $tmp_articleType) {
			if (TRUE) {
				$aRGB[$datasetsKey]["r"] = array_rand(array_fill(0, 254, true)); //+1
				$aRGB[$datasetsKey]["g"] = array_rand(array_fill(0, 254, true)); //+1
				$aRGB[$datasetsKey]["b"] = array_rand(array_fill(0, 254, true)); //+1
				$data["areaChartData"]["datasets"][$datasetsKey]["label"] = $tmp_articleType;
				$data["areaChartData"]["datasets"][$datasetsKey]["backgroundColor"] = "rgba({$aRGB[$datasetsKey]["r"]}, {$aRGB[$datasetsKey]["g"]}, {$aRGB[$datasetsKey]["b"]}, 0.9)";
				$data["areaChartData"]["datasets"][$datasetsKey]["borderColor"] = "rgba({$aRGB[$datasetsKey]["r"]}, {$aRGB[$datasetsKey]["g"]}, {$aRGB[$datasetsKey]["b"]}, 0.8)";
				$data["areaChartData"]["datasets"][$datasetsKey]["pointRadius"] = "3";
				//$data["areaChartData"]["datasets"][$datasetsKey]["pointColor"] = "#3b8bba";
				// $data["areaChartData"]["datasets"][$datasetsKey]["pointStrokeColor"] = "rgba({$r}, {$g}, {$b}, 1)";
				//$data["areaChartData"]["datasets"][$datasetsKey]["pointHighlightFill"] = "#fff";
				// $data["areaChartData"]["datasets"][$datasetsKey]["pointHighlightStroke"] = "rgba({$r}, {$g}, {$b}, 1)";
				$data["areaChartData"]["datasets"][$datasetsKey]["spanGaps"] = true;
				$data["areaChartData"]["datasets"][$datasetsKey]["fill"] = false;
				$data["areaChartData"]["datasets"][$datasetsKey]["data"] = array_fill(0, count($data["areaChartData"]["labels"])-1, null);
			}

			$this->db->select("brd_insertDate, round(avg({$this->scoreISP})) as brd_ispScore");
			$this->db->where("{$this->scoreISP} !=", "None");
			$this->db->like("brd_articleType", $tmp_articleType);
			$this->db->group_by("brd_insertDate");
			$this->db->order_by("brd_insertDate");
			$query = $this->db->get("fruit_Test");
			if (!empty($query) && $query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$labelsKey = array_search($row->brd_insertDate, $data["areaChartData"]["labels"]);
					$data["areaChartData"]["datasets"][$datasetsKey]["data"][$labelsKey] = $row->brd_ispScore;
				}
			}
		}
		
		$data = $this->getStackedBarChartData($data, $aRGB);
		file_put_contents("./application/logs/dump_getLineChartData.txt", print_r($data, TRUE));
		print json_encode($data);
	}

	public function getStackedBarChartData($data, $aRGB) {
		$this->scoreISP = $this->uri->segments[3];
		$data["result"] = FALSE;
		$this->db->select("brd_insertDate");
		$this->db->where("brd_insertDate !=", "2021-12-16");
		$this->db->group_by("brd_insertDate");
		$this->db->order_by("brd_insertDate");
		$query = $this->db->get("fruit_Test");
		if (!empty($query) && $query->num_rows() > 0) {
			foreach ($query->result() as $key => $row) {
				$data["stackedBarChartData"]["labels"][$key] = $row->brd_insertDate;
			}
		}
		//file_put_contents("./application/logs/dump.txt", print_r($data, TRUE));
		foreach (array("5G", "資費", "收訊", "網速", "其他") as $datasetsKey => $tmp_articleType) {
			if (TRUE) {
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["label"] = $tmp_articleType;
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["backgroundColor"] = "rgba({$aRGB[$datasetsKey]["r"]}, {$aRGB[$datasetsKey]["g"]}, {$aRGB[$datasetsKey]["b"]}, 0.9)";
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["borderColor"] = "rgba({$aRGB[$datasetsKey]["r"]}, {$aRGB[$datasetsKey]["g"]}, {$aRGB[$datasetsKey]["b"]}, 0.8)";
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["pointRadius"] = "3";
				//$data["stackedBarChartData"]["datasets"][$datasetsKey]["pointColor"] = "#3b8bba";
				// $data["stackedBarChartData"]["datasets"][$datasetsKey]["pointStrokeColor"] = "rgba({$r}, {$g}, {$b}, 1)";
				//$data["stackedBarChartData"]["datasets"][$datasetsKey]["pointHighlightFill"] = "#fff";
				// $data["stackedBarChartData"]["datasets"][$datasetsKey]["pointHighlightStroke"] = "rgba({$r}, {$g}, {$b}, 1)";
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["spanGaps"] = true;
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["fill"] = false;
				$data["stackedBarChartData"]["datasets"][$datasetsKey]["data"] = array_fill(0, count($data["stackedBarChartData"]["labels"])-1, null);
			}

			$this->db->select("brd_insertDate, count({$this->scoreISP}) as brd_ispCounter");
			$this->db->where("{$this->scoreISP} !=", "None");
			// $this->db->where("brd_insertDate !=", "2021-12-16");
			$this->db->like("brd_articleType", $tmp_articleType);
			$this->db->group_by("brd_insertDate");
			$this->db->order_by("brd_insertDate");
			$query = $this->db->get("fruit_Test");
			//file_put_contents("./application/logs/dump.txt", print_r($this->db, TRUE));
			if (!empty($query) && $query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$labelsKey = array_search($row->brd_insertDate, $data["stackedBarChartData"]["labels"]);
					$data["stackedBarChartData"]["datasets"][$datasetsKey]["data"][$labelsKey] = $row->brd_ispCounter;
				}
			}
		}
		//file_put_contents("./application/logs/dump.txt", print_r($data, TRUE));
		return $data;
	}

	public function getAjaxRawdata() {
		$this->scoreISP = $this->uri->segments[3];
		$aPOST = $_POST;

		$data["draw"] = intVal($aPOST["draw"]);
		$data["recordsTotal"] = intVal($aPOST["length"]);
		$data["recordsFiltered"] = 0;

		if (TRUE) {
			//$this->db->where("brd_insertDate", "2021-12-22");
			$this->db->where("{$this->scoreISP} !=", "N/A");
			if (!empty($aPOST["brd_sourceWeb"]) || !empty($aPOST["brd_articleType"]) || !empty($aPOST["brd_pushType"])) {
				$this->db->group_start();
				if (!empty($aPOST["brd_sourceWeb"])) $this->db->where("brd_sourceWeb", $aPOST["brd_sourceWeb"]);
				if (!empty($aPOST["brd_articleType"])) $this->db->like("brd_articleType", $aPOST["brd_articleType"]);
				if (!empty($aPOST["brd_pushType"]) && $aPOST["brd_pushType"] == "主文") $this->db->like("brd_pushType", $aPOST["brd_pushType"]);
				if (!empty($aPOST["brd_pushType"]) && $aPOST["brd_pushType"] == "回文") {
					$this->db->group_start();
					$this->db->like("brd_pushType", "推文");
					$this->db->or_like("brd_pushType", "回文");
					$this->db->group_end();
				}
				$this->db->group_end();
			}
			if (!empty($aPOST["search"]["value"])) {
				$this->db->group_start();
				$this->db->like("brd_title", $aPOST["search"]["value"]);
				$this->db->or_like("brd_contents", $aPOST["search"]["value"]);
				$this->db->or_like("brd_insertDate", $aPOST["search"]["value"]);
				$this->db->group_end();
			}
			$query = $this->db->get("fruit_Test");
			if (!empty($query) && $query->num_rows() >= 0) $data["recordsFiltered"] = $query->num_rows();
		}

		//$this->db->where("brd_insertDate", "2021-12-22");
		$this->db->where("{$this->scoreISP} !=", "None");
		if (!empty($aPOST["brd_sourceWeb"]) || !empty($aPOST["brd_articleType"]) || !empty($aPOST["brd_pushType"])) {
			$this->db->group_start();
			if (!empty($aPOST["brd_sourceWeb"])) $this->db->where("brd_sourceWeb", $aPOST["brd_sourceWeb"]);
			if (!empty($aPOST["brd_articleType"])) $this->db->like("brd_articleType", $aPOST["brd_articleType"]);
			if (!empty($aPOST["brd_pushType"]) && $aPOST["brd_pushType"] == "主文") $this->db->like("brd_pushType", $aPOST["brd_pushType"]);
			if (!empty($aPOST["brd_pushType"]) && $aPOST["brd_pushType"] == "回文") {
				$this->db->group_start();
				$this->db->like("brd_pushType", "推文");
				$this->db->or_like("brd_pushType", "回文");
				$this->db->group_end();
			}
			$this->db->group_end();
		}

		if (!empty($aPOST["search"]["value"])) {
			$this->db->group_start();
			$this->db->like("brd_title", $aPOST["search"]["value"]);
			$this->db->or_like("brd_contents", $aPOST["search"]["value"]);
			$this->db->or_like("brd_insertDate", $aPOST["search"]["value"]);
			$this->db->group_end();
		}
		$this->db->limit(intVal($aPOST["length"]), intVal($aPOST["start"]));
		if ($aPOST["columns"][$aPOST["order"][0]["column"]]["data"] == "brd_ispScore") {
			$this->db->order_by("{$this->scoreISP} {$aPOST["order"][0]["dir"]}");
		} else {
			$this->db->order_by("{$aPOST["columns"][$aPOST["order"][0]["column"]]["data"]} {$aPOST["order"][0]["dir"]}");
		}
		file_put_contents("./application/logs/dump.txt", print_r($this, TRUE), FILE_APPEND);
		$query = $this->db->get("fruit_Test");
		file_put_contents("./application/logs/dump.txt", print_r($query, TRUE), FILE_APPEND);
		file_put_contents("./application/logs/dump.txt", print_r($this->db, TRUE), FILE_APPEND);
		if (!empty($query) && $query->num_rows() > 0) {
			foreach ($query->result() as $key => $row) {
				$data["data"][$key]["brd_sourceWeb"] = $row->brd_sourceWeb ?? "";
				$data["data"][$key]["brd_articleType"] = $row->brd_articleType ?? "";
				$data["data"][$key]["brd_title"] = mb_substr($row->brd_title, 0, 20, "utf-8") . "..." ?? "";
				$data["data"][$key]["brd_contents"] = "<a href='{$row->brd_soureUrl}' target='_blank'>" . mb_substr($row->brd_contents, 0, 20, "utf-8") . "..." . "</a>" ?? "";
				$data["data"][$key]["brd_pushType"] = $row->brd_pushType ?? "";
				$data["data"][$key]["brd_ispScore"] = $row->{$this->scoreISP} ?? "";
				$data["data"][$key]["brd_insertDate"] = $row->brd_insertDate ?? "";
			}
		}
		$data["data"] = $data["data"] ?? array();

		file_put_contents("./application/logs/dump.txt", print_r($aPOST, TRUE));
		print json_encode($data);
	}
}
