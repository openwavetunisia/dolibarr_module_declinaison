<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2012      Marcos García        <marcosgdf@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/product/liste.php
 *  \ingroup    produit
 *  \brief      Page to list products and services
 */
//var_dump($_REQUEST);exit;
require 'config.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
dol_include_once('/declinaison/class/declinaison.class.php');
dol_include_once('/declinaison/lib/declinaison.lib.php');
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("products");
$langs->load("stocks");
$langs->load("declinaison@declinaison");

$action = GETPOST('action');
$sref=GETPOST("sref");
$sbarcode=GETPOST("sbarcode");
$snom=GETPOST("snom");
$sall=GETPOST("sall");
$type=GETPOST("type","int");
$search_sale = GETPOST("search_sale");
$search_categ = GETPOST("search_categ",'int');
$tosell = GETPOST("tosell");
$tobuy = GETPOST("tobuy");
$fourn_id = GETPOST("fourn_id",'int');
$catid = GETPOST('catid','int');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="p.fk_parent, p.ref";
if (! $sortorder) $sortorder="ASC";

$limit = $conf->liste_limit;

$fk_product = GETPOST('fk_product', 'int');
if($fk_product==0) exit('Erreur fk_product missing');

$product=new Product($db);
$product->fetch($fk_product);

$resql=$db->query("SELECT fk_parent FROM ".MAIN_DB_PREFIX."declinaison WHERE fk_declinaison=".$fk_product);
$objp = $db->fetch_object($resql);
if($objp->fk_parent==0) {
	$is_declinaison_master=true;
	$fk_parent_declinaison = $fk_product;
}
else {
	$is_declinaison_master=false;
	$fk_parent_declinaison = $objp->fk_parent;
}

if($action=='create_declinaison' && ($user->rights->produit->creer || $user->rights->service->creer) ) {
	
	if(isset($_REQUEST['create_dec'])) { // Uniquement si on se trouve sur une création standard de déclinaison (dans les autres cas on ne crée pas de nouveau produit)
	
		$dec = new Product($db);
		$dec->fetch($fk_parent_declinaison);
		$dec->fetch_optionals($dec->id);
	
		$libelle = GETPOST('libelle_dec');
		$dec->libelle=($libelle) ? $libelle : $dec->libelle.' (déclinaison)';
		
		$ref_added = GETPOST('add_reference_dec');
		
		$dec->ref=GETPOST('reference_dec').' '.$ref_added; 
	    $dec->id = null;
		
	     // Gére le code barre
	    if ($conf->barcode->enabled) {
	    	$module = strtolower($conf->global->BARCODE_PRODUCT_ADDON_NUM);
	    	$result = dol_include_once('/core/modules/barcode/'.$module.'.php');
	    	if ($result > 0) {
				$modBarCodeProduct =new $module();

				$tmpcode = $modBarCodeProduct->getNextValue($dec, 'int');
				$dec->barcode = $tmpcode;
	    	}
			else{
				$dec->barcode = '';
			}
	    }
	
	}
    
	if ((($conf->global->DECLINAISON_ALLOW_CREATE_DECLINAISON_WITH_EXISTANT_PRODUCTS > 0) && isset($_REQUEST['create_dec_with_existant_prod'])) || $dec->check()){
		
		if(isset($_REQUEST['create_dec'])) {

			$id_clone = $dec->create($user);
			
			//pre($dec,true);exit;
			
			if (!empty($conf->global->PRODUIT_MULTIPRICES)) 
			{
				foreach($dec->multiprices as $i => $price){
					
					if(GETPOST('more_price')) $price += GETPOST('more_price');
					if(GETPOST('more_percent')) $price = $price * ( 1 + (GETPOST('more_percent') / 100 ));
					$dec->updatePrice($price, $dec->multiprices_base_type[$i], $user, $dec->multiprices_tva_tx[$i],'', $i);
				}
			}
			
			//var_dump($dec);
			//$dec->clone_associations($fk_parent_declinaison, $id_clone);
		  	
			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{
				$result=$dec->insertExtraFields();
			}
			
		}
		
		if($id_clone>0 || (($conf->global->DECLINAISON_ALLOW_CREATE_DECLINAISON_WITH_EXISTANT_PRODUCTS > 0) && isset($_REQUEST['create_dec_with_existant_prod']))) {
		
			
			$TPDOdb = new TPDOdb;
			
			$newDeclinaison = new TDeclinaison;
			$newDeclinaison->fk_parent = GETPOST('fk_product');
			if(isset($_REQUEST['create_dec'])) $newDeclinaison->fk_declinaison = $dec->id;
			elseif(isset($_REQUEST['create_dec_with_existant_prod'])) $newDeclinaison->fk_declinaison = GETPOST('productid');
			$newDeclinaison->up_to_date = 1;
			$newDeclinaison->ref_added = $ref_added;
			
			if(isset($_REQUEST['create_dec'])) {
				
	            $newDeclinaison->more_price = GETPOST('more_price');
	            $newDeclinaison->more_percent = GETPOST('more_percent');
				
            } elseif(isset($_REQUEST['create_dec_with_existant_prod'])) {
            	
	            $newDeclinaison->more_price = GETPOST('more_price_with_existant_product');
	            $newDeclinaison->more_percent = GETPOST('more_percent_with_existant_product');
				            	
            }
			
			if($newDeclinaison->fk_declinaison > 0) $newDeclinaison->save($TPDOdb);

		}
		else {
			if($id_clone==-1) {
				setEventMessage($langs->trans('ErrorProductAlreadyExists', $dec->ref), 'errors');
			}
			else {
					
				print 'clone:'.(int)$id_clone.'<br />';
				dol_print_error($db,$dec->error);
				
			}
			
		}
	}
		
	else {
		print "check : ";
		dol_print_error($db,$dec->error);
	}
}

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
//$object->getCanvas($id);
$canvas=GETPOST("canvas");
$objcanvas='';
if (! empty($canvas))
{
    require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
    $objcanvas = new Canvas($db,$action);
    $objcanvas->getCanvas('product','list',$canvas);
}

// Security check
if ($type=='0') $result=restrictedArea($user,'produit','','','','','',$objcanvas);
else if ($type=='1') $result=restrictedArea($user,'service','','','','','',$objcanvas);
else $result=restrictedArea($user,'produit|service','','','','','',$objcanvas);


/*
 * Actions
 */

if (isset($_POST["button_removefilter_x"]))
{
	$sref="";
	$sbarcode="";
	$snom="";
	$search_categ=0;
}

/*
 * View
 */

$htmlother=new FormOther($db);
$form=new Form($db);

        $title=$langs->trans("Declinaison");
        llxHeader('',$title,$helpurl,'');
        
        $head=product_prepare_head($product, $user);
        $titre=$langs->trans("CardProduct".$product->type);
        $picto=($product->type==1?'service':'product');
        dol_fiche_head($head, 'declinaison', $titre, 0, $picto);
        
         
        ?>
            <table class="border" width="100%">
                <tr>
                    <td><?php echo $langs->trans("Ref"); ?></td>
                    <td><?php echo $product->ref; ?></td>
                </tr>
                <tr>
                    <td><?php echo $langs->trans("Label"); ?></td>
                    <td><?php echo $product->libelle; ?></td>
                </tr>
            </table><br />      
        <?php


    form_declinaison_create_new($product, $fk_parent_declinaison);
    liste_my_declinaison($product, $fk_parent_declinaison);
    
    form_declinaison_maj();
    liste_iam_a_declinaison($product, $fk_parent_declinaison);


?>
<script type="text/javascript">
function quickEditProduct(fk_product) {

	if($('#quickEditProduct').length==0) {
		$('body').append('<div id="quickEditProduct" title="Edition rapide"></div>');
	}

	$.get("<?php 
	   if((float)DOL_VERSION<=3.6) echo dol_buildpath('/product/fiche.php?action=edit&id=',1);
       else   echo dol_buildpath('/product/card.php?action=edit&id=',1);
       
	?>"+fk_product, function(data) {
		var html = $(data).find('div.fiche').html();


		$('#quickEditProduct').html(html);
		$('#quickEditProduct input[name=cancel]').remove();

		$('#quickEditProduct form').submit(function() {

			$.post($(this).attr('action'), $( this ).serialize(), function() {

				$('#quickEditProduct').dialog("close");
				$.jnotify('Modifications enregistr&eacute;es', "ok");   
	
				refreshDeclinaisonList();
				
			} );


			return false;
		});

		refreshDeclinaisonList();

		$('#quickEditProduct').dialog({
			modal:true,
			width:'80%'
		});

	});

}

function refreshDeclinaisonList() {
	$.get(document.location.href, function(data) {
		$('#listDeclinaison').replaceWith( $(data).find('#listDeclinaison'));
		
		<?php
		if($conf->global->DECLINAISON_NO_MODIFY_ITEM==1) {
			?>removeLinkDeclinaison();
		<?php
		}
		?>
		
	});
}
function removeLinkDeclinaison() {
		
		$('#listDeclinaison a.quickedit').remove();
		/*$('#listDeclinaison a').each(function() {
			$(this).replaceWith( $(this).html() );
		});*/
		
		$('#libelle_dec,#reference_dec').css('background-color','#ccc');
		$('#libelle_dec,#reference_dec').attr('readonly','readonly');
		
	
	
}
<?php
	if(!empty($id_clone) && $id_clone>0) {
		if(!$conf->global->DECLINAISON_SILENT_MODE) {
			?>
			quickEditProduct(<?php echo $id_clone; ?>);
			<?php
		}
		else {
			?>refreshDeclinaisonList();
			$.jnotify('D&eacute;clinaison cr&eacute;&eacute;e', "ok");   
			<?php
		}
		
	}
	
	if($conf->global->DECLINAISON_NO_MODIFY_ITEM==1) {
		?>removeLinkDeclinaison();<?php
	}
	
?>
</script>

<?php

llxFooter();
$db->close();
