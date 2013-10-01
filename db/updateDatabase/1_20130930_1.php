<?php
$g_majorDb = 1;
$g_minorDb = 20130930;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130930_1(Zend_Db_Adapter_Abstract $db)
{

    try {
        $query = sprintf("
CREATE TABLE tmp_community_report_register (
  `report_uid`              varchar(32) NOT NULL,
  `client_name`             varchar(255),
  `is_plhiv_paba`           bool,
  `fe_provided_preventive`  bool,
  `fe_provided_clinical`    bool,
  `fe_provided_supportive`  bool,
  `plwha_young_male`        bool,
  `plwha_young_female`      bool,
  `plwha_adult_male`        bool,
  `plwha_adult_female`      bool,
  `paba_young_male`         bool,
  `paba_young_female`       bool,
  `paba_adult_male`         bool,
  `paba_adult_female`       bool,
  `other_young_male`        bool,
  `other_young_female`      bool,
  `other_adult_male`        bool,
  `other_adult_female`      bool,
  `se_provided_preventive`  bool,
  `se_provided_clinical`    bool,
  `se_provided_supportive`  bool,
  `preventive_1`            bool,
  `preventive_2`            bool,
  `preventive_3`            bool,
  `preventive_4`            bool,
  `preventive_5`            bool,
  `preventive_6`            bool,
  `supportive_out_1`        bool,
  `supportive_out_2`        bool,
  `supportive_out_3`        bool,
  `supportive_out_4`        bool,
  `supportive_out_5`        bool,
  `supportive_out_6`        bool,
  `supportive_in_1`         bool,
  `supportive_in_2`         bool,
  `supportive_in_3`         bool,
  `supportive_in_4`         bool,
  `supportive_in_5`         bool,
  `clinical_sti_1`          bool,
  `clinical_sti_2`          bool,
  `clinical_malaria_1`      bool,
  `clinical_malaria_2`      bool,
  `clinical_malaria_3`      bool,
  `clinical_reproductive_1` bool,
  `clinical_reproductive_2` bool,
  `clinical_reproductive_3` bool,
  `clinical_reproductive_4` bool,
  `clinical_tb_1`           bool,
  `clinical_tb_2`           bool,
  `clinical_tb_3`           bool,
  `clinical_tb_4`           bool,
  `clinical_tb_5`           bool,
  `clinical_palliative_1`   bool,
  `clinical_palliative_2`   bool,
  `adr_screened`            bool,
  `adr_not_detected`        bool,
  `adr_detected`            bool,
  `nafdac_filled`           bool,
  `adr_intervention_1`      bool,
  `adr_intervention_2`      bool,
  `adr_intervention_3`      bool
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}