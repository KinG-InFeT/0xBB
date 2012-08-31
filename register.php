<?php
/**************************************************************************
 * 		        		0xBB ~ Bullettin Board						      *
 **************************************************************************
 * This program is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or   
 * (at your option) any later version.                                  
 *                                                                      
 * This program is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of       
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        
 * GNU General Public License for more details.                         
 *                                                                      
 * You should have received a copy of the GNU General Public License    
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * =========================================================================*
 * Software:					0xBB
 * Software version:			2.0
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =========================================================================*
 * register.php                                                        
 ***************************************************************************/
session_start();

include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

if (login ($username, $password))
	_err("<b>Errore!</b>Sei già Registrato/Loggato al Forum!");

if (!check_block_register())
	_err("Le Inscrizioni al Forum sono momentaneamente Chiuse!");

if (@$_GET['action'] == 'register') {

    //aquisisco i dati dalla form
    @$username    = clear($_POST ['username']);
    @$pass        = $_POST ['pass'];
    @$pass_check  = $_POST ['pass_check'];
    @$email       = clear($_POST['email']);
    @$email_check = clear($_POST['email_check']);
    @$web_site    = clear($_POST['web_site']);
    @$msn         = clear($_POST['msn']);
    
    $error_msg = array();
    
    if(empty($_POST['captcha']))
		$error_msg[] = 'Inserire Captcha!';
	
	if($_POST['captcha'] != $_SESSION['captcha'])
		$error_msg[] = 'Captcha inserito non corretto! Riprovare.';

	if (empty($username))
		$error_msg[] = 'Nessun Username Inserito!';
		
	if(empty($pass) && empty($pass_check))
		$error_msg[] = 'Inserire La password e il controllo password!';
	
	if(empty($email) && empty($email_check))
		$error_msg[] = 'Inserire la E-Mail e il controllo Mail!';
		
	if(check_email_register($email) == FALSE)
		$error_msg[] = 'Email utilizzata da un\'altro account.';
	
	if(check_exist_user($username))
		$error_msg[] = 'L\'Username è già esistente!';

	if(!($pass == $pass_check))
		$error_msg[] = 'Le password inserite non combaciano';
		
	if(!(check_email($email)))
		$error_msg[] = 'L\' E-Mail inserita non è valida';		
		
	if(!($email == $email_check))
		$error_msg[] = 'Le E-Mail inserite non combaciano';
		
	if(!(empty($msn)))
		if(!(check_email($msn)))
			$error_msg[] = 'Il contatto MsN inserito non è valido';
			
	if(!(empty($web_site)))
		if(check_url($web_site) == FALSE)
			$error_msg[] = 'Il Sito Web inserito non è valido';
			
	if(strlen($username) > 20)
		$error_msg[] = 'L\'username è troppo lungo ( Max. 20 caratteri )';

	if(!($error_msg)) {

			$pass  = md5 ($pass);
			$query = "INSERT INTO ". __PREFIX__ ."users (
						username, password, level, email, web_site, msn, theme
					) VALUES (
						'". $username ."', '{$pass}', 'user', '{$email}', '{$web_site}', '{$msn}', 'default.css')";
									
			mysql_query($query) or _err(mysql_error());
			
			$sql = "INSERT INTO ". __PREFIX__ ."karma (
						vote_user_id, vote
					) VALUES (
						'".nick2uid($username)."', '0')";
											
			mysql_query($sql) or _err(mysql_error());
			
			//ban ip inserimenti IP
			$ban_ip = "INSERT INTO ". __PREFIX__ ."ban_ip (
						user_id, ip, banned
					) VALUES (
						'".nick2uid($username)."', '".$_SERVER['REMOTE_ADDR']."', '0')";
						
			mysql_query($ban_ip) or _err(mysql_error());
		
		$oggetto   = "Benvenuto in ".SITE_NAME.".";
		$messaggio = "Ciao ".$username."\n"
					."Siamo lieti di darti il benvenuto in ".SITE_NAME."\n"
					."I tuoi dati di accesso sono:\n\n"
					."Username: ".$username."\n"
					."Password: ".clear($pass_check)."\n\n"
					."Ti auguriamo una buona permanenza,\n"
					."Lo Staff ~ ".SITE_NAME.".";
					
		$check_send_mail = @mail($email, $oggetto, $messaggio,"From: ".$email);
		
		if($check_send_email == TRUE)
			die("<div class=\"success_msg\" align=\"center\">\nRegistrazione Avvenuta con Successo!\n<br /><p>E-Mail di Benvenuto Inviata!</p><br />\n<a href=\"login.php\">Vai al Login</a></div>");
		else
			die("<div class=\"success_msg\" align=\"center\">\nRegistrazione Avvenuta con Successo!\n<br /><p>E-Mail di Benvenuto non Inviata!</p><br />\n<a href=\"login.php\">Vai al Login</a></div>");
	
	}else{
		print "\n<div class=\"error_msg\">"
            . "\n<h3 align=\"center\">Errori nella form!</h2><br />"
            . "\n<br /><center>";
			  
 		foreach($error_msg as $error_message) {
			print $error_message." <br />\n";
		}
			
		print "<br />\n<a href='javascript:history.back()'>Torna Indietro</a>\n</center>\n</div>\n";
	}
}else{

    $_SESSION['n1'] = rand(1,20);
    $_SESSION['n2'] = rand(1,20);
    $_SESSION['captcha'] = $_SESSION['n1'] + $_SESSION['n2'];
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<div class = 'register'>
<br /><br />
	<form method = 'POST' action = 'register.php?action=register'>
		<table align = "center">
			<tr><td>Username:</td><td><input name = 'username' type = 'text' maxlength='20'>*</td></tr>
			<tr><td>Password:</td><td><input name = 'pass' type = 'password'>*</td></tr>
			<tr><td>Password (again):</td><td><input name = 'pass_check' type = 'password'>*</td></tr>
			<tr><td>E-Mail:</td><td><input name = 'email' type = 'text'>*</td></tr>
			<tr><td>E-Mail (again):</td><td><input name = 'email_check' type = 'text'>*</td></tr>
			<tr><td>Web Site:</td><td><input name = 'web_site' type = 'text'>&nbsp;</td></tr>
			<tr><td>You MsN:</td><td><input name = 'msn' type = 'text'>&nbsp;</td></tr>
			<tr><td>Captcha: <?php print $_SESSION['n1'] ." + ". $_SESSION['n2']; ?></td><td><input name = 'captcha' type = 'text'>*</td></tr>
			<tr><td colspan="2>"><br />* : I Campi sono Obbligatori!<br /><p><input value = 'Register' type = 'submit' /> <input value = 'Reset' type = 'reset' /></p></td></tr>
		</table>
	</form>
<?php 
} //chiudo l'else per il controllo errori
?>
	</div>
</div>
<?php
footer();
?>
</body>
</html>
