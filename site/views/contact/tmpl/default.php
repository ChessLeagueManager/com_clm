<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fred Baumgarten
*/

defined('_JEXEC') or die('Restricted access');

$model = $this->getModel();
$clmuser = $model->getCLMClmuser();

// Login Status prüfen
?>
<div id="contactdata">
<style type="text/css">
	#clm .clm table th, #clm .clm table td { padding-left: 0.1em; padding-top: 0.2em; padding-right: 0.1em; padding-bottom: 0.2em; border: 1px solid #CCCCCC; }
</style>
<?php
if (isset($clmuser[0])) {
    ?>
<h4><?php echo JText::_('CONTACT_DATA_OF')." ".$clmuser[0]->name; ?></h4>
<form>
<script language="javascript" type="text/javascript">
function emailsyntax(element) {
	let regex = new RegExp("([!#-'*+/-9=?A-Z^-~-]+(\.[!#-'*+/-9=?A-Z^-~-]+)*|\"\(\[\]!#-[^-~ \t]|(\\[\t -~]))+\")@([!#-'*+/-9=?A-Z^-~-]+\\\.([!#-'*+/-9=?A-Z^-~-]+)|\[[\t -Z^-~]*])");

	address = element.value;
	if (regex.test(address)) {
		return 0;
	}
	return 1;
}

function phonesyntax(element) {
	number = element.value;
	minlen0=2;
	maxlen0=4;
	minlen1=6;
	maxlen1=8;
	minlen2=12;
	maxlen2=16;
	if (element.id == "mobile") {
		minlen1=7;
		maxlen1=7;
		minlen2=15;
		maxlen2=16;
	}
	if (number.length == 0) {
		return 0;
	}
	if (number.charCodeAt(0) == 40) {
		console.log("Klammer auf");
		number = number.substr(1,number.length-1);
		element.value = number;
	}
	if (number.length == 0) {
		return 0;
	}
	if (number.charCodeAt(0) == 48) {
		if (number.length == 1) {
			number = "+49 ";
			element.value = number;
		} else {
			number = "+49 " + number.substr(1,number.length-1);
			element.value = number;
		}
	}
	if (number.charCodeAt(0) != 43) {
		return 1;
	}
	c=1;
	while (number.charCodeAt(c) != 32) {
		if (c >= number.length) return 2;
		if (number.charCodeAt(c) < 48) {
			return 1;
		}
		if (number.charCodeAt(c) > 57) {
			return 1;
		}
		c++;
		if ((c == 2) && (number.length >= 2)) {
			z1 = number.charCodeAt(1);
			if ((z1 == 49) || (z1 == 55)) {
				if (number.length > 2) {
					if (number.charCodeAt(2) != 32) {
						number = number.substr(0,2) + " " + number.substr(2,number.length-2);
						element.value = number;
					}
				} else {
					number = number.substr(0,2) + " ";
					element.value = number;
				}
			}
		}
		if ((c == 3) && (number.length >= 3)) {
			z1 = number.charCodeAt(1);
			z2 = number.charCodeAt(2);
			zweier = 0;
			if ((z1 == 50) && (z2 == 48)) zweier = 1;
			if ((z1 == 50) && (z2 == 55)) zweier = 1;
			if ((z1 == 50) && (z2 == 56)) return 1;
			if ((z1 == 51) && (z2 == 48)) zweier = 1;
			if ((z1 == 51) && (z2 == 49)) zweier = 1;
			if ((z1 == 51) && (z2 == 50)) zweier = 1;
			if ((z1 == 51) && (z2 == 51)) zweier = 1;
			if ((z1 == 51) && (z2 == 52)) zweier = 1;
			if ((z1 == 51) && (z2 == 54)) zweier = 1;
			if ((z1 == 51) && (z2 == 57)) zweier = 1;
			if ((z1 == 52) && (z2 == 48)) zweier = 1;
			if ((z1 == 52) && (z2 == 49)) zweier = 1;
			if ((z1 == 52) && (z2 == 51)) zweier = 1;
			if ((z1 == 52) && (z2 == 52)) zweier = 1;
			if ((z1 == 52) && (z2 == 53)) zweier = 1;
			if ((z1 == 52) && (z2 == 54)) zweier = 1;
			if ((z1 == 52) && (z2 == 55)) zweier = 1;
			if ((z1 == 52) && (z2 == 56)) zweier = 1;
			if ((z1 == 52) && (z2 == 57)) zweier = 1;
			if ((z1 == 53) && (z2 == 49)) zweier = 1;
			if ((z1 == 53) && (z2 == 50)) zweier = 1;
			if ((z1 == 53) && (z2 == 51)) zweier = 1;
			if ((z1 == 53) && (z2 == 52)) zweier = 1;
			if ((z1 == 53) && (z2 == 53)) zweier = 1;
			if ((z1 == 53) && (z2 == 54)) zweier = 1;
			if ((z1 == 53) && (z2 == 55)) zweier = 1;
			if ((z1 == 53) && (z2 == 56)) zweier = 1;
			if ((z1 == 54) && (z2 == 48)) zweier = 1;
			if ((z1 == 54) && (z2 == 49)) zweier = 1;
			if ((z1 == 54) && (z2 == 50)) zweier = 1;
			if ((z1 == 54) && (z2 == 51)) zweier = 1;
			if ((z1 == 54) && (z2 == 52)) zweier = 1;
			if ((z1 == 54) && (z2 == 53)) zweier = 1;
			if ((z1 == 54) && (z2 == 54)) zweier = 1;
			if ((z1 == 56) && (z2 == 49)) zweier = 1;
			if ((z1 == 56) && (z2 == 50)) zweier = 1;
			if ((z1 == 56) && (z2 == 51)) zweier = 1;
			if ((z1 == 56) && (z2 == 52)) zweier = 1;
			if ((z1 == 56) && (z2 == 54)) zweier = 1;
			if ((z1 == 56) && (z2 == 57)) return 1;
			if ((z1 == 57) && (z2 == 48)) zweier = 1;
			if ((z1 == 57) && (z2 == 49)) zweier = 1;
			if ((z1 == 57) && (z2 == 50)) zweier = 1;
			if ((z1 == 57) && (z2 == 51)) zweier = 1;
			if ((z1 == 57) && (z2 == 52)) zweier = 1;
			if ((z1 == 57) && (z2 == 53)) zweier = 1;
			if ((z1 == 57) && (z2 == 56)) zweier = 1;
			if (zweier == 1) {
				if (number.length > 3) {
					if (number.charCodeAt(3) != 32) {
						number = number.substr(0,3) + " " + number.substr(3,number.length-3);
						element.value = number;
					}
				} else {
					number = number.substr(0,3) + " ";
					element.value = number;
				}
			}
		}
	}

	if (number.substr(0,3) == "+49") {
		if (c < 3) {
			return 1;
		}
		console.log("Deutschland !");
		if (number.charCodeAt(c) != 32) {
			return 1;
		}
		c++;
		while (number.charCodeAt(c) == 32) {
			number = number.substr(0,c) + number.substr(c+1,number.length-1);
			element.value = number;
			if (c >= number.length) return 2;
		}
		if (number.charCodeAt(c) == 48) {
			number = number.substr(0,c) + number.substr(c+1,number.length-1);
			element.value = number;
			if (c >= number.length) return 2;
		}
		while (number.charCodeAt(c) != 32) {
			if (c >= number.length) return 2;
			if (number.charCodeAt(c) == 40) {
				number = number.substr(0,c) + number.substr(c+1,number.length-1);
				element.value = number;
				if (c >= number.length) return 2;
				if (number.charCodeAt(c) == 32) break;
				if (number.charCodeAt(c) == 48) {
					number = number.substr(0,c) + number.substr(c+1,number.length-1);
					element.value = number;
					if (c >= number.length) return 2;
					if (number.charCodeAt(c) == 32) break;
				}
			}
			if (number.charCodeAt(c) == 41) {
				if (number.charCodeAt(c - 1) != 32) {
					number = number.substr(0,c) + " " + number.substr(c+1,number.length-1);	// +49 (0)221 2346219  +49 0221 23478   0221 265417   +49 2262/472692     02262/591434    (02242) 3327
				} else {
					number = number.substr(0,c) + number.substr(c+1,number.length-1);	// +49 (0)221 2346219
				}
				element.value = number;
				if (c >= number.length) return 2;
				if (number.charCodeAt(c) == 32) break;
			}
			if (number.charCodeAt(c) == 47) {
				if (number.charCodeAt(c - 1) != 32) {
					number = number.substr(0,c) + " " + number.substr(c+1,number.length-1);	// +49 (0)221 2346219
				} else {
					number = number.substr(0,c) + number.substr(c+1,number.length-1);	// +49 (0)221 2346219
				}
				element.value = number;
				if (c >= number.length) return 2;
				if (number.charCodeAt(c) == 32) break;
			}
			if (number.charCodeAt(c) < 48) {
				return 1;
			}
			if (number.charCodeAt(c) > 57) {
				return 1;
			}
			c++;
			if ((c == maxlen1) && (number.length == maxlen1)) {
				number = number + " ";
				element.value = number;
			}
		}
		c++;
		if (c >= number.length) return 2;
		while (number.charCodeAt(c) == 32) {
			number = number.substr(0,c) + number.substr(c+1,number.length-1);
			element.value = number;
			if (c >= number.length) return 2;
		}
		while (number.charCodeAt(c) != 32) {
			if (number.charCodeAt(c) == 41) {
				number = number.substr(0,c) + number.substr(c+1,number.length-1);
				element.value = number;
				if (c >= number.length) return 2;
			}
			if (number.charCodeAt(c) < 48) {
				return 1;
			}
			if (number.charCodeAt(c) > 57) {
				return 1;
			}
			c++;
			if (number.length > maxlen2) return 1;
			if ((c >= number.length) && (c >= minlen2)) return 0;
			if (c >= number.length) return 2;
		}
		return 1;
	} else {
		if (number.length < 12) return 2;
		if (number.length > maxlen2) return 1;
	}
	return 0;
}

function updateemail(id) {
	element = document.getElementById(id);
	button = document.getElementById("contactbutton");
	rv = emailsyntax(element);
	if (rv == 1) {
		element.style.background="#ff0000";
		button.disabled = true;
	} else {
		element.style.background="#ffffff";
		button.disabled = false;
	}
}

function updatephone(id) {
	element = document.getElementById(id);
	button = document.getElementById("contactbutton");
	rv = phonesyntax(element);
	if (rv == 1) {
		element.style.background="#ff0000";
		button.disabled = true;
	} else {
		if (rv == 2) {
			element.style.background="#ffff00";
			button.disabled = true;
		} else {
			element.style.background="#ffffff";
			button.disabled = false;
		}
	}
}
</script>
<table class="rangliste" frame="box" border="1">
<?php echo "<tr><td>" . JText::_('CONTACT_FIXED') ."</td><td> "
            . $clmuser[0]->tel_fest . "</td><td><input onInput=\"updatephone('fixed')\" id=\"fixed\" name=\"fixed\" value=\"" . $clmuser[0]->tel_fest . "\"/></td></tr>\n";
    echo "<tr><td>" . JText::_('CONTACT_MOBILE') ."</td><td>"
      . $clmuser[0]->tel_mobil . "</td><td><input onInput=\"updatephone('mobile')\" id=\"mobile\" name=\"mobile\" value=\"" . $clmuser[0]->tel_mobil . "\"/></td></tr>\n";
    //CLM parameter auslesen
    $clm_config = clm_core::$db->config();
    if ($clm_config->email_independent == 0) {
        echo "<tr><td>"	. JText::_('CONTACT_EMAIL') ."</td><td>"
            . $clmuser[0]->email . "</td><td><input onInput=\"updateemail('email')\" id=\"email\" name=\"email\" value=\"" . $clmuser[0]->email . "\"/></td></tr>\n";
    } else {
        echo "<tr><td>"	. JText::_('CONTACT_EMAIL_CLM') ."</td><td>"
            . $clmuser[0]->email . "</td><td><input onInput=\"updateemail('email')\" id=\"email\" name=\"email\" value=\"" . $clmuser[0]->email . "\"/></td></tr>\n";
        echo "<tr><td>"	. JText::_('CONTACT_EMAIL_JOOMLA') ."</td><td>"
            . $clmuser[0]->jmail . "</td><td><input onInput=\"updateemail('jmail')\" id=\"jmail\" name=\"jmail\" value=\"" . $clmuser[0]->jmail . "\"/></td></tr>\n";
    }
    echo "<tr><td colspan=\"2\"><input type=\"hidden\" name=\"view\" value=\"contact\"></td><td><input type=\"submit\" id=\"contactbutton\" value=\"" . JText::_('CONTACT_BUTTON') . "\"></td></tr>\n";
    ?>
</table>
<br/>
<?php echo JText::_('CONTACT_HILFE'); ?>
<br/>
<?php if ($clm_config->email_independent == 1) {
    echo "<br>".JText::_('CONTACT_HILFE1');
}
    ?>

</form>
<br>
</div>
<?php
}
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
?>

