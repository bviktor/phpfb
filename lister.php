<?php
/***************************************************************************

   Copyright (c) 2006-2009, Berke Viktor
   All rights reserved.
   
   Redistribution and use in source and binary forms, with or without
   modification, are permitted provided that the following conditions
   are met:
   1. Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
   2. Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
   3. The name of the author may not be used to endorse or promote products
      derived from this software without specific prior written permission.
   
   THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
   IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
   OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
   IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
   INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
   NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
   DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
   THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
   (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
   THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

***************************************************************************/

/* options */
$gfxdir = '@gfx';
$up_size = '2097152';
$chmod_enabled = '1';
/* end of options */

/* bootstrap */
$phpfb_ver = 'trunk';
$wdir = getcwd();

$dir1 = ltrim($_GET[dir], "/");

$messages = array(
	"perm_error" => "You are not allowed to browse the selected directory."
);

/* set doctype of output */
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">';

$dir2 = str_replace(" ", "%20", $dir1);

$back1 = explode("/", $dir1);
$back2 = ((count($back1)) - 2);

$i = 0;
while ($back2 > $i)
{
	$back3 = $back3.'/'.$back1[$i];
	$i++;
}

/* display header file if it exists */
if (file_exists($dir1.'@HEADER'))
{
	$header = file_get_contents($dir1.'@HEADER');
}

/* if folder is set to private, return to parent folder */
if (file_exists($dir1.'@PRIVATE') or strpos($dir1, '..') > -1)
{
	echo '<head><meta http-equiv="refresh" content="0,url=lister.php?priv=1&dir='.$back3.'" /></head></html>';
	die;
}

if ($_GET[priv] == 1)
{
	$message = $messages["perm_error"];
}
/* end of bootstrap */

/* start of viewer */
if ($_GET[view] == 1)
{
	$img1 = $_GET[fn];
	$img2 = str_replace(" ", "%20", $img1);
	$pic = explode("/", $img2);
	$picname2 = $pic[(count($pic))-1];
	$picname1 = str_replace("%20", " ", $picname2);
	$pic_back = rtrim($img2, "$picname2");
	$reso = getimagesize($img1);
	$res_w = $reso[0];
	$res_h = $reso[1];
	
	/* some grammar */
	$size_str = (string)number_format(filesize($img1)/1024, 0);
	if ($size_str > 1)
	{
		$size_str = $size_str." kBytes";
	}
	else
	{
		$size_str = $size_str." kByte";
	}

	/* output the result */
	echo '<head><title>'.$picname1.'</title></head><body link="#0000cc" alink="#0000cc" vlink="#0000cc"><div>
	<img alt="back" src="'.$gfxdir.'/back.png" border="0" /><a href="lister.php?dir='.$pic_back.'">Back</a><br /><br />
	<b>Filename: </b><a href="'.$img2.'">'.$picname1.'</a><br />
	<b>Filesize: </b>'.$size_str.'<br />
	<b>Resolution: </b>'.$res_w.' x '.$res_h.' pixels<br />
	<hr width="'.($reso[0] + 2).'" align="left" />
	<img alt="image" src="'.$img2.'" width="'.$res_w.'" height="'.$res_h.'" border="1" />
	<hr width="'.($reso[0] + 2).'" align="left" />';
} 
/* end of viewer */

/* start of lister */
else
{
	//  >>>>>  LISTER  >>>>>
	$the_array = Array();
	$handle = opendir($wdir.'/'.$dir1);
	
	while (false !== ($file1 = readdir($handle)))
	{
		$file2 = str_replace(" ", "%20", $file1);
		if ($file2 != $dir1.'lister.php' && substr($file2, 0,1) != '@' && substr($file2, 0,1) != '.' xor substr($file2, 0,2) == '..')
		{
			$the_array[] = $file2;
		}
	}

	closedir($handle);
	sort ($the_array);
	reset ($the_array);
	while (list ($key, $val2) = each ($the_array))
	{
		$val1 = str_replace("%20", " ", $val2);

		if ($val1 == '..' || is_file($dir1.$val1) == false)
		{
			$f_icon = 'folder.png';
			$f_size = '-';
			$f_link = 'lister.php?dir='.$dir2.$val2.'/';
			$f_alt= '[DIR]';
		}
		else
		{
			$f_size = number_format(filesize($dir1.$val1)/1024, 0, ',', ' ').' kB';
			$f_alt= '[ . ]';
			if ($f_size == 0)
			{
				$f_icon = 'unknown.png';
				$f_link = $dir2.$val2;
			}
			else
			{
				$f_check = getimagesize($dir1.$val1);
				if ( $f_check[2] == '' )
				{
					$f_icon = 'unknown.png';
					$f_link = $dir2.$val2;
				}
				else
				{
					$f_icon = 'image.png'; 
					if (file_exists($dir1.'@VIEWER'))
					{
						$f_link = 'lister.php?view=1&amp;fn='.$dir2.$val2;
					}
					else
					{
						$f_link = $dir2.$val2;
					}
				}
			}
		}
		
		if ($val1 != '..')
		{
			$f_date = date("d-m-Y H:i", filemtime($dir1.$val1));
		}

		if ($val1 == '..')
		{
			$f_link = 'lister.php?dir='.$back3.'/';
			$val1 = 'Parent Directory';
			$f_icon = 'back.png'; 
			$f_date ='';
			$f_size = '-';
		}

		if ((strlen($val1)) > 50)
		{
			$val1 = substr($val1, 0, 47).'...';
		}

		$file_list .= '<table border="0" cellspacing="0" cellpadding="0"><tr>
			<td width="500"><tt><img alt="'.$f_alt.'" src="'.$gfxdir.'/'.$f_icon.'" /> <a href="'.$f_link.'">'.$val1.'</a></tt></td>
			<td width="180" align="right"><tt>'.$f_date.'</tt></td>
			<td width="110" align="right"><tt>'.$f_size.'</tt></td>
			</tr></table>';
	}

	echo '<head><title>Index of /'.$dir1.'</title></head><body link="#0000cc" alink="#0000cc" vlink="#0000cc">
		<h1>Index of <a href="lister.php?dir='.$dir2.'" style="text-decoration: none;"><font color="#000000">/'.$dir1.'</font></a></h1>
		<div><table border="0" cellspacing="0" cellpadding="0"><tr>
		<td width="500"><tt><img alt="spacer" src="'.$gfxdir.'/blank.png" /> Name</tt></td>
		<td width="180" align="right"><tt>Last Modified</tt></td>
		<td width="110" align="right"><tt>Size</tt></td>
		</tr></table><hr />';
	if ($header)
	{
		echo '<table border="0" cellspacing="0" cellpadding="0"><tr><td width="500"><tt>'.$header.'</tt></td></tr></table><br />';
	}
	echo $file_list.'<hr />';
/* end of lister */

/* start of uploader */
	if (file_exists($dir1.'@UPLOAD'))
	{
		$up_size2 = file_get_contents($dir1.'@UPLOAD');
		if ($up_size2 != '')
		{
			$up_size = $up_size2;
		}

		$upload_dir = $wdir.'/'.$dir1;
		$upload_url = 'http://'.$_SERVER['HTTP_HOST'].$dir1;

		function do_upload($upload_dir, $upload_url, $up_size, $chmod_enabled)
		{
			$temp_name = $_FILES['userfile']['tmp_name'];
			$file_name = $_FILES['userfile']['name']; 
			$result    = $_FILES['userfile']['error'];

			$file_name2 = explode('.', $file_name);
			$file_ext = strtoupper($file_name2[(count($file_name2) - 1)]);

			if (file_exists($upload_dir.$file_name))
			{
				$message = 'The file already exists!';
				return $message;
			}
			if ($_FILES['userfile']['size'] > $up_size)
			{
				$message = 'The file is too big!';
				return $message;
			}
			if ($file_ext == 'MP3' or $file_ext == 'WMA')
			{
				$message = $file_ext.' upload is disabled.';
				return $message;
			}

			if ($chmod_enabled == '1')
			{
				chmod($temp_name, 0644);
			}

			if ($file_ext == 'PHP')
			{
				$result = move_uploaded_file($temp_name, $upload_dir.$file_name.'s');
			}
			else
			{
				$result = move_uploaded_file($temp_name, $upload_dir.$file_name);
			}

			$message = ($result)?'Success! Click on the folder name to see your file!' : 'Upload failed!';
			return $message;
		}

		if ($_FILES['userfile'])
		{
			$message = do_upload($upload_dir, $upload_url, $up_size, $chmod_enabled);
		}

		echo '<form name="upload" enctype="multipart/form-data" method="post" action="">
			<span class="textm">Uploader ('.number_format($up_size/1048576, 2, ',', ' ').' MB): </span>
			<input type="file" id="userfile" name="userfile" />
			<input type="submit" name="upload" value="Upload" /></form><hr />';

	}
/* end of uploader */
}

/* start of footer */
echo '</div><address>phpFB '.$phpfb_ver.'</address></body></html>';

if ($message)
{
	echo '<script language="JavaScript">window.alert("'.$message.'");</script>';
}
/* end of footer */
?>
