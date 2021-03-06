<?php
require_once Mage::getBaseDir('lib').'/trm/Services/Twilio.php';

define("ISENABLED", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliosetupgroup/enabled',Mage::app()->getStore()));

define("SENDTOTAL", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwilioadmingroup/ordertotal',Mage::app()->getStore()));
define("NEWORDERENABLED", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwilioadmingroup/neworderenable',Mage::app()->getStore()));
define("NEWORDERRECIPIENTS", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwilioadmingroup/neworderrecipients',Mage::app()->getStore()));

define("CUSTOMERISENABLED", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliocustomergroup/enabled',Mage::app()->getStore()));
define("TRACKINGISENABLED", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliocustomergroup/trackingenable',Mage::app()->getStore()));
define("SENDTO", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliocustomergroup/sendto',Mage::app()->getStore()));
define("ALLOWSPECIFIC", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliocustomergroup/sallowspecific',Mage::app()->getStore()));
define("SPECIFICCOUNTRY", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliocustomergroup/specificcountry',Mage::app()->getStore()));

define("ACCOUNTSID", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliosetupgroup/accountsid',Mage::app()->getStore()));
define("AUTHTOKEN", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliosetupgroup/authtoken',Mage::app()->getStore()));
define("TWILIONUMBER", Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliosetupgroup/twiliophonenumber',Mage::app()->getStore()));
define("STORENAME", Mage::app()->getStore()->getName());

class Trmmarketing_Twiliosmsbridge_Model_Observer
{
 
	
    public function checkout(Varien_Event_Observer $observer)
    {
		//$newOrderEnabled = Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwilioadmingroup/neworderenable',Mage::app()->getStore());
		//$newOrderReceipients = Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwilioadmingroup/neworderrecipients',Mage::app()->getStore());
		
		
		if((ISENABLED)&&(NEWORDERENABLED)):
		
		$order = $observer->getEvent()->getOrder();
		$orderNumber = $order->getIncrementId();
		$grandTotal = $order->getGrandTotal();
		
		// Instantiate a new Twilio Rest Client
		$client = new Services_Twilio(ACCOUNTSID, AUTHTOKEN);
		$from = preg_replace("/[^0-9]/","",TWILIONUMBER);
		$recipients = explode(",", NEWORDERRECIPIENTS);
		
		// Send a new outgoing SMS */
		$body .= STORENAME . " " . Mage::helper('twiliosmsbridge')->__('new order placed. #') . $orderNumber;
		// Append order total if enabled
		if(SENDTOTAL) $body .= " " . Mage::helper('twiliosmsbridge')->__('Order Amount: $') . $grandTotal;
		
		
		foreach ($recipients as $recipient) :
			try {
				$recipient = preg_replace("/[^0-9]/","",$recipient);
				
				
				$client->account->sms_messages->create($from, $recipient, $body);
				$object = Mage::getModel('twiliosmsbridge/twiliosmsbridge');
				$object->setTitle($orderNumber);
				$object->setContent($body);
				$object->setCreatedTime(now());
				$object->save();  
			
			} catch (Exception $e) { 
			
			 }
		
    	endforeach;



	endif;
    }
	
	
	public function tracking(Varien_Event_Observer $observer)
    {
		
		//$trackingenable = Mage::getStoreConfig('trmtwiliosmsbridgeconfig/trmtwiliocustomergroup/trackingenable',Mage::app()->getStore());
		
		if((ISENABLED)&&(CUSTOMERISENABLED)):
		
		
		$allowCountries = explode(",", SPECIFICCOUNTRY);
		//$order = $observer->getEvent()->getOrder();
		//$orderNumber = $order->getIncrementId();
		$shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
		
		//get store order was placed in
		$store = Mage::app()->getStore()->load($order->getStoreId());
		 

        //do something with order - get the increment id:
        $orderNumber = $order->getIncrementId();
		
		
		$billingAddress = $order->getBillingAddress();
		$shippingAddress = $order->getShippingAddress();
	
		$countryToCode = array(
			"93" => "AF",
			"355" => "AL",
			"213" => "DZ",
			"1684" => "AS",
			"376" => "AD",
			"244" => "AO",
			"1264" => "AI",
			"672" => "AQ",
			"1268" => "AG",
			"54" => "AR",
			"374" => "AM",
			"297" => "AW",
			"61" => "AU",
			"43" => "AT",
			"994" => "AZ",
			"1242" => "BS",
			"973" => "BH",
			"880" => "BD",
			"1246" => "BB",
			"375" => "BY",
			"32" => "BE",
			"501" => "BZ",
			"229" => "BJ",
			"1441" => "BM",
			"975" => "BT",
			"591" => "BO",
			"387" => "BA",
			"267" => "BW",
			"55" => "BR",
			"1284" => "VG",
			"673" => "BN",
			"359" => "BG",
			"226" => "BF",
			"95" => "MM",
			"257" => "BI",
			"855" => "KH",
			"237" => "CM",
			"1" => "CA",
			"238" => "CV",
			"1345" => "KY",
			"236" => "CF",
			"235" => "TD",
			"56" => "CL",
			"86" => "CN",
			"61" => "CX",
			"61" => "CC",
			"57" => "CO",
			"269" => "KM",
			"682" => "CK",
			"506" => "CR",
			"385" => "HR",
			"53" => "CU",
			"357" => "CY",
			"420" => "CZ",
			"243" => "CD",
			"45" => "DK",
			"253" => "DJ",
			"1767" => "DM",
			"1809" => "DO",
			"593" => "EC",
			"20" => "EG",
			"503" => "SV",
			"240" => "GQ",
			"291" => "ER",
			"372" => "EE",
			"251" => "ET",
			"500" => "FK",
			"298" => "FO",
			"679" => "FJ",
			"358" => "FI",
			"33" => "FR",
			"689" => "PF",
			"241" => "GA",
			"220" => "GM",
			"995" => "GE",
			"49" => "DE",
			"233" => "GH",
			"350" => "GI",
			"30" => "GR",
			"299" => "GL",
			"1473" => "GD",
			"1671" => "GU",
			"502" => "GT",
			"224" => "GN",
			"245" => "GW",
			"592" => "GY",
			"509" => "HT",
			"39" => "VA",
			"504" => "HN",
			"852" => "HK",
			"36" => "HU",
			"354" => "IS",
			"91" => "IN",
			"62" => "ID",
			"98" => "IR",
			"964" => "IQ",
			"353" => "IE",
			"44" => "IM",
			"972" => "IL",
			"39" => "IT",
			"225" => "CI",
			"1876" => "JM",
			"81" => "JP",
			"962" => "JO",
			"7" => "KZ",
			"254" => "KE",
			"686" => "KI",
			"965" => "KW",
			"996" => "KG",
			"856" => "LA",
			"371" => "LV",
			"961" => "LB",
			"266" => "LS",
			"231" => "LR",
			"218" => "LY",
			"423" => "LI",
			"370" => "LT",
			"352" => "LU",
			"853" => "MO",
			"389" => "MK",
			"261" => "MG",
			"265" => "MW",
			"60" => "MY",
			"960" => "MV",
			"223" => "ML",
			"356" => "MT",
			"692" => "MH",
			"222" => "MR",
			"230" => "MU",
			"262" => "YT",
			"52" => "MX",
			"691" => "FM",
			"373" => "MD",
			"377" => "MC",
			"976" => "MN",
			"382" => "ME",
			"1664" => "MA",
			"258" => "MZ",
			"264" => "NA",
			"674" => "NR",
			"977" => "NP",
			"31" => "NL",
			"599" => "AN",
			"687" => "NC",
			"64" => "NZ",
			"505" => "NI",
			"227" => "NE",
			"234" => "NG",
			"683" => "NU",
			"672" => "NF",
			"850" => "KP",
			"1670" => "MP",
			"47" => "NO",
			"968" => "OM",
			"92" => "PK",
			"680" => "PW",
			"507" => "PA",
			"675" => "PG",
			"595" => "PY",
			"51" => "PE",
			"63" => "PH",
			"870" => "PN",
			"48" => "PL",
			"351" => "PT",
			"1" => "PR",
			"974" => "QA",
			"242" => "CG",
			"40" => "RO",
			"7" => "RU",
			"250" => "RW",
			"590" => "BL",
			"290" => "SH",
			"1869" => "KN",
			"1758" => "LC",
			"1599" => "MF",
			"508" => "PM",
			"1784" => "VC",
			"685" => "WS",
			"378" => "SM",
			"239" => "ST",
			"966" => "SA",
			"221" => "SN",
			"381" => "RS",
			"248" => "SC",
			"232" => "SL",
			"65" => "SG",
			"421" => "SK",
			"386" => "SI",
			"677" => "SB",
			"252" => "SO",
			"27" => "ZA",
			"82" => "KR",
			"34" => "ES",
			"94" => "LK",
			"249" => "SD",
			"597" => "SR",
			"268" => "SZ",
			"46" => "SE",
			"41" => "CH",
			"963" => "SY",
			"886" => "TW",
			"992" => "TJ",
			"255" => "TZ",
			"66" => "TH",
			"670" => "TL",
			"228" => "TG",
			"690" => "TK",
			"676" => "TO",
			"1868" => "TT",
			"216" => "TN",
			"90" => "TR",
			"993" => "TM",
			"1649" => "TC",
			"688" => "TV",
			"256" => "UG",
			"380" => "UA",
			"971" => "AE",
			"44" => "GB",
			"1" => "US",
			"598" => "UY",
			"1340" => "VI",
			"998" => "UZ",
			"678" => "VU",
			"58" => "VE",
			"84" => "VN",
			"681" => "WF",
			"967" => "YE",
			"260" => "ZM",
			"263" => "ZW",	
		);
		
		function checkPhone($phone, $countryCode){

		if (substr($phone, 0, strlen($countryCode) ) === (string)$countryCode) :
		   return $phone;
		   else:
		   return $countryCode.$phone;
		endif;	
			
		}
		
		
		function clean($string) {
		   return preg_replace('/\D/', '', $string); 
		}
		//get country code and clean phone numbers
		$billingphone = clean($billingAddress->getTelephone());
		$billingCountryCode = array_search($billingAddress->getCountry(), $countryToCode);
		$billingphone = checkPhone($billingphone, $billingCountryCode);
		$shippingphone = $shippingAddress->getTelephone();
		$shippingCountryCode = array_search($shippingAddress->getCountry(), $countryToCode);
		$shippingphone = checkPhone($shippingphone, $shippingCountryCode);
		
		// get tracking information
		if(TRACKINGISENABLED):
		$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();
				
			foreach ($shipmentCollection as $shipment):
				
					if($shipment->getAllTracks() ) $trackinginformation .= ' ' . Mage::helper('twiliosmsbridge')->__('Tracking #');	
					foreach($shipment->getAllTracks() as $tracknum):
							
						$trackinginformation .= ' ' . $tracknum->getTitle() . ': ' . $tracknum->getNumber() . ' ';
							
					endforeach;
						
			endforeach;
		endif;
						// end tracking information
		
		
		// Instantiate a new Twilio Rest Client
		$client = new Services_Twilio(ACCOUNTSID, AUTHTOKEN);
		$from = preg_replace("/[^0-9]/","",TWILIONUMBER);
		
		//create recipients list
		$recipients = array();
		
		if(((ALLOWSPECIFIC)&&(in_array($billingAddress->getCountry(),$allowCountries)))||(!ALLOWSPECIFIC))
		if((SENDTO == 'billing')||(SENDTO == 'both'))
		if (!in_array($recipients, $billingphone)) array_push($recipients, $billingphone);
		
		if(((ALLOWSPECIFIC)&&(in_array($shippingAddress->getCountry(),$allowCountries)))||(!ALLOWSPECIFIC))
		if((SENDTO == 'shipping')||(SENDTO == 'both')) 
		if (!in_array($recipients, $shippingphone)) array_push($recipients, $shippingphone); 
		
		$body = $store->getName() . " " . Mage::helper('twiliosmsbridge')->__('shipped order #') .  $orderNumber . $trackinginformation;
		
		foreach ($recipients as $recipient) :
				
				try {
					
					$recipient = preg_replace("/[^0-9]/","",$recipient);
					// Send a new outgoing SMS */
					
					$client->account->sms_messages->create($from, $recipient, $body);
					$object = Mage::getModel('twiliosmsbridge/twiliosmsbridge');
					$object->setTitle($orderNumber.'sent to: '.$recipient);
					$object->setContent($body);
					$object->setCreatedTime(now());
					$object->save();
			
				} catch (Exception $e) { 
			
			 }  
		
    	endforeach;



	endif;
    }
	
	
	
	
	
	
}