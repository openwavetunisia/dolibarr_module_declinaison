<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		lib/advancedcomposition.lib.php
 *	\ingroup	advancedcomposition
 *	\brief		This file is an example module library
 *				Put some comments here
 */

function declinaisonAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("declinaison@declinaison");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/declinaison/admin/declinaison_setup.php", 1);
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'settings';
    $h++;
    $head[$h][0] = dol_buildpath("/declinaison/admin/declinaison_about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@advancedcomposition:/advancedcomposition/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@advancedcomposition:/advancedcomposition/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'advancedcomposition');

    return $head;
}

function form_declinaison_create_new(&$product, $fk_parent_declinaison) {
    global $langs, $db, $conf,$user;
    
    $fk_product = $product->id;
    
    if ($user->rights->produit->creer || $user->rights->service->creer) {
    /* c'est la déclinaison parente */  
        $add_ref=chr(65+$num); 
    
        $form = new Form($db);
    
        ?>
        <p>
            <form name="form_declinaison" action="liste.php">
                
            <input type="hidden" name="action" value="create_declinaison" />
            
            <input type="hidden" name="fk_product" value="<?php echo $fk_product; ?>" /> 
            <input type="hidden" name="fk_parent_declinaison" value="<?php echo $fk_parent_declinaison; ?>" /> 
            
            
            <table class="border" width="100%">
                <tr>
                    <td><?php echo $langs->trans("Ref"); ?></td>
                    <td><input type="text" name="reference_dec" id="reference_dec" value="<?php echo $product->ref; ?>" size="30" maxlength="255" />
                    <input type="text" name="add_reference_dec" id="add_reference_dec" value="<?php echo $add_ref; ?>" size="5" maxlength="50" /></td>
                    <?php
                        if($conf->global->DECLINAISON_ALLOW_CREATE_DECLINAISON_WITH_EXISTANT_PRODUCTS) {
                            ?>
                                <td width="50%" colspan="2" rowspan="2">
                                    <?php $form->select_produits('', 'productid', ''); ?>
                                </td>
                            <?php
                        }
                    ?>
                </tr>
                <tr>
                    <td width="20%"><?php echo $langs->trans("Label"); ?></td>
                    <td><input type="text" name="libelle_dec" id="libelle_dec" value="<?php echo addslashes($product->libelle).' '.$add_ref; ?>" size="40" maxlength="255" initlibelle="<?php echo addslashes($product->libelle); ?>" /></td>
                </tr>
                <tr> 
                    <td><?php echo $langs->trans('MirrorPriceMore'); ?></td><td><input type="number" step="0.01" name="more_price" value="<?php echo $re->more_price ?>" onchange=" if(this.value!=0) $('input[name=more_percent]').val(0) " /></td>
                    <?php
                        if($conf->global->DECLINAISON_ALLOW_CREATE_DECLINAISON_WITH_EXISTANT_PRODUCTS) {
                            ?><td width="20%"><?php echo $langs->trans('MirrorPriceMore'); ?></td><td><input type="number" step="0.01" name="more_price_with_existant_product" value="<?php echo $re->more_price ?>" onchange=" if(this.value!=0) $('input[name=more_percent_with_existant_product]').val(0) " /></td><?php
                        }
                    ?>
                </tr>
                 <tr>
                    <td><?php echo $langs->trans('MirrorPricePercent'); ?></td><td><input type="number" step="1" name="more_percent" value="<?php echo $re->more_percent ?>"  onchange=" if(this.value!=0) $('input[name=more_price]').val(0) "  /></td>
                    <?php
                        if($conf->global->DECLINAISON_ALLOW_CREATE_DECLINAISON_WITH_EXISTANT_PRODUCTS) {
                            ?><td><?php echo $langs->trans('MirrorPricePercent'); ?></td><td><input type="number" step="1" name="more_percent_with_existant_product" value="<?php echo $re->more_percent ?>"  onchange=" if(this.value!=0) $('input[name=more_price_with_existant_product]').val(0) "  /></td><?php
                        }
                    ?>
                 </tr>
                 <tr>
                    <td colspan="2">
                        <center><button type="submit" name="create_dec" class="butAction" ><?php echo $langs->trans('CreateNewDeclinaison') ?></button></center>
                    </td>
                    <?php
                    if($conf->global->DECLINAISON_ALLOW_CREATE_DECLINAISON_WITH_EXISTANT_PRODUCTS) {
                        ?>
                            <td colspan="2">
                                <center><button type="submit" name="create_dec_with_existant_prod" class="butAction" ><?php echo $langs->trans('CreateNewDeclinaisonWithExistantProduct') ?></button></center>
                            </td>
                        <?php
                    }
                    ?>
                 </tr>   
            </table>
            </form>
        <br />
        <br />
        </p>
        <script type="text/javascript">
            
            $('#add_reference_dec').keyup(function() {
                var DECLINAISON_NO_MODIFY_ITEM = <?php echo (int)$conf->global->DECLINAISON_NO_MODIFY_ITEM; ?>;
                var ref = $(this).val();
                
                var libelle = $('#libelle_dec').attr('initlibelle');
                    
                $('#libelle_dec').val( libelle +' ' + ref );
                
            });
            
        </script>
        <?php
        
    }
}

function form_declinaison_maj($fk_product = 0) {
    global $db, $langs, $conf;
    
        if(empty($fk_product)) $fk_product = GETPOST('fk_product');
                
        if(GETPOST('action')=='SAVE_DECLINAISON') {
            //Le produit est une déclinaison
            //echo($_REQUEST['up_to_date']);
            //if($_REQUEST['up_to_date'] == "Oui") {
            $sql = "UPDATE ".MAIN_DB_PREFIX."declinaison";
            $sql.= " SET up_to_date = ".( GETPOST('up_to_date') ? 1 : 0 );
            $sql.= " ,more_price=".(float)GETPOST('more_price');
            $sql.= " ,more_percent=".(float)GETPOST('more_percent');
            $sql.= " WHERE fk_declinaison = ".$fk_product;

            $db->query($sql);
            
            setEventMessage("Modification enregistrée avec succès");
        }
        ?>
        
            <form name="priceUpToDate" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" />
                <p>
                    
                    <?php                   
                        //On récupère la valeur actuelle du champ "up_to_date" pour cette déclinaison
                        $sql = "SELECT up_to_date,more_price,more_percent";
                        $sql.= " FROM ".MAIN_DB_PREFIX."declinaison";
                        $sql.= " WHERE fk_declinaison = ".$fk_product;
                        $result = $db->query($sql);
                        $re = $db->fetch_object($result);
                    ?>
                    <input type="hidden" name="action" value="SAVE_DECLINAISON" />
                    <input type="hidden" name="fk_product" value="<?php echo $fk_product; ?>" />
                    <table>
                        <tr>
                            <td><?php echo $langs->trans('MirrorPrice'); ?></td><td><input type="checkbox" name="up_to_date" value="1" <?php if ($re->up_to_date){ ?>checked="checked"<?php } ?>/></td>
                         </tr>
                         <tr> 
                            <td><?php echo $langs->trans('MirrorPriceMore'); ?></td><td><input type="number" step="0.01" name="more_price" value="<?php echo $re->more_price ?>" onchange=" if(this.value!=0) $('input[name=more_percent]').val(0) " /></td>
                         </tr>
                         <tr>
                            <td><?php echo $langs->trans('MirrorPricePercent'); ?></td><td><input type="number" step="1" name="more_percent" value="<?php echo $re->more_percent ?>"  onchange=" if(this.value!=0) $('input[name=more_price]').val(0) "  /></td>
                         </tr>   
                        <tr><td colspan="2" align="center">
                                                    <input type="submit" name="maintientAJour" value="Valider" />
                        </td></tr>
                    </table>
                    <!--<?php print $form->selectyesno("sync_price_dec",$object->public,1);?>-->
                    
                <br />
                </p>
            </form>
        <?php
    
    
}

function liste_my_declinaison(&$product, $fk_parent_declinaison) {
    global $db, $langs, $conf, $htmlother, $form;
    
    print_titre("Déclinaisons du produit");
    
    $sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,';
    $sql.= ' p.fk_product_type, p.tms as datem,';
    $sql.= ' p.duration, p.tosell, p.tobuy, p.seuil_stock_alerte,';
    $sql.= ' MIN(pfp.unitprice) as minsellprice';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
    if (! empty($search_categ) || ! empty($catid)) $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX."categorie_product as cp ON p.rowid = cp.fk_product"; // We'll need this table joined to the select in order to filter by categ
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_fournisseur_price as pfp ON p.rowid = pfp.fk_product";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."declinaison as d ON d.fk_declinaison = p.rowid";
    $sql.= ' WHERE p.entity IN ('.getEntity('product', 1).') AND (d.fk_parent='.$fk_parent_declinaison." OR p.rowid=$fk_parent_declinaison)";
    if ($sall)
    {
        $sql.= " AND (p.ref LIKE '%".$db->escape($sall)."%' OR p.label LIKE '%".$db->escape($sall)."%' OR p.description LIKE '%".$db->escape($sall)."%' OR p.note LIKE '%".$db->escape($sall)."%'";
        if (! empty($conf->barcode->enabled))
        {
            $sql.= " OR p.barcode LIKE '%".$db->escape($sall)."%'";
        }
        $sql.= ')';
    }
    // if the type is not 1, we show all products (type = 0,2,3)
    if (dol_strlen($type))
    {
        if ($type == 1) $sql.= " AND p.fk_product_type = '1'";
        else $sql.= " AND p.fk_product_type <> '1'";
    }
    if ($sref)     $sql.= " AND p.ref LIKE '%".$sref."%'";
    if ($sbarcode) $sql.= " AND p.barcode LIKE '%".$sbarcode."%'";
    if ($snom)     $sql.= " AND p.label LIKE '%".$db->escape($snom)."%'";
    if (isset($tosell) && dol_strlen($tosell) > 0) $sql.= " AND p.tosell = ".$db->escape($tosell);
    if (isset($tobuy) && dol_strlen($tobuy) > 0)   $sql.= " AND p.tobuy = ".$db->escape($tobuy);
    if (dol_strlen($canvas) > 0)                    $sql.= " AND p.canvas = '".$db->escape($canvas)."'";
    if ($catid > 0)    $sql.= " AND cp.fk_categorie = ".$catid;
    if ($catid == -2)  $sql.= " AND cp.fk_categorie IS NULL";
    if ($search_categ > 0)   $sql.= " AND cp.fk_categorie = ".$search_categ;
    if ($search_categ == -2) $sql.= " AND cp.fk_categorie IS NULL";
    if ($fourn_id > 0) $sql.= " AND pfp.fk_soc = ".$fourn_id;
    $sql.= " GROUP BY p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,";
    $sql.= " p.fk_product_type, p.tms,";
    $sql.= " p.duration, p.tosell, p.tobuy, p.seuil_stock_alerte";
    //if (GETPOST("toolowstock")) $sql.= " HAVING SUM(s.reel) < p.seuil_stock_alerte";    // Not used yet
    $sql.= $db->order($sortfield,$sortorder);
    $sql.= $db->plimit($limit + 1, $offset);
    
    dol_syslog("sql=".$sql);
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);

        $i = 0;


        $helpurl='';
        if (isset($type))
        {
            if ($type == 0)
            {
                $helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
            }
            else if ($type == 1)
            {
                $helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
            }
        }

       
        // Displays product removal confirmation
        if (GETPOST('delprod')) dol_htmloutput_mesg($langs->trans("ProductDeleted",GETPOST('delprod')));

        $param="&fk_product=".$fk_product."&amp;sref=".$sref.($sbarcode?"&amp;sbarcode=".$sbarcode:"")."&amp;snom=".$snom."&amp;sall=".$sall."&amp;tosell=".$tosell."&amp;tobuy=".$tobuy;
        $param.=($fourn_id?"&amp;fourn_id=".$fourn_id:"");
        $param.=($search_categ?"&amp;search_categ=".$search_categ:"");
        $param.=isset($type)?"&amp;type=".$type:"";
        print_barre_liste('', $page, "liste.php", $param, $sortfield, $sortorder,'',$num);

        if (! empty($catid))
        {
            print "<div id='ways'>";
            $c = new Categorie($db);
            $ways = $c->print_all_ways(' &gt; ','product/liste.php');
            print " &gt; ".$ways[0]."<br>\n";
            print "</div><br>";
        }

            print '<form action="liste.php" method="post" name="formulaire">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="list">';
            print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
            print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
            print '<input type="hidden" name="type" value="'.$type.'">';
            print '<input type="hidden" name="fk_product" value="'.$fk_product.'">';

            print '<table id="listDeclinaison" class="liste" width="100%">';

            // Filter on categories
            $moreforfilter='';
            if (! empty($conf->categorie->enabled))
            {
                $moreforfilter.=$langs->trans('Categories'). ': ';
                $moreforfilter.=$htmlother->select_categories(0,$search_categ,'search_categ',1);
                $moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
            }
            if ($moreforfilter)
            {
                print '<tr class="liste_titre">';
                print '<td class="liste_titre" colspan="9">';
                print $moreforfilter;
                print '</td></tr>';
            }

            // Lignes des titres
            print "<tr class=\"liste_titre\">";
            print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "p.ref",$param,"","",$sortfield,$sortorder);
            print_liste_field_titre($langs->trans("Label"), $_SERVER["PHP_SELF"], "p.label",$param,"","",$sortfield,$sortorder);
            if (! empty($conf->barcode->enabled)) print_liste_field_titre($langs->trans("BarCode"), $_SERVER["PHP_SELF"], "p.barcode",$param,'','',$sortfield,$sortorder);
            print_liste_field_titre($langs->trans("DateModification"), $_SERVER["PHP_SELF"], "p.tms",$param,"",'align="center"',$sortfield,$sortorder);
            if (! empty($conf->service->enabled) && $type != 0) print_liste_field_titre($langs->trans("Duration"), $_SERVER["PHP_SELF"], "p.duration",$param,"",'align="center"',$sortfield,$sortorder);
            if (empty($conf->global->PRODUIT_MULTIPRICES)) print_liste_field_titre($langs->trans("SellingPrice"), $_SERVER["PHP_SELF"], "p.price",$param,"",'align="right"',$sortfield,$sortorder);
            print '<td class="liste_titre" align="right">'.$langs->trans("BuyingPriceMinShort").'</td>';
            if (! empty($conf->stock->enabled) && $user->rights->stock->lire && $type != 1) print '<td class="liste_titre" align="right">'.$langs->trans("PhysicalStock").'</td>';
            print_liste_field_titre($langs->trans("Sell"), $_SERVER["PHP_SELF"], "p.tosell",$param,"",'align="right"',$sortfield,$sortorder);
            print_liste_field_titre($langs->trans("Buy"), $_SERVER["PHP_SELF"], "p.tobuy",$param,"",'align="right"',$sortfield,$sortorder);
            print "</tr>\n";

            // Lignes des champs de filtre
            print '<tr class="liste_titre">';
            print '<td class="liste_titre" align="left">';
            print '<input class="flat" type="text" name="sref" size="8" value="'.$sref.'">';
            print '</td>';
            print '<td class="liste_titre" align="left">';
            print '<input class="flat" type="text" name="snom" size="12" value="'.$snom.'">';
            print '</td>';
            if (! empty($conf->barcode->enabled))
            {
                print '<td class="liste_titre">';
                print '<input class="flat" type="text" name="sbarcode" size="6" value="'.$sbarcode.'">';
                print '</td>';
            }
            print '<td class="liste_titre">';
            print '&nbsp;';
            print '</td>';

            // Duration
            if (! empty($conf->service->enabled) && $type != 0)
            {
                print '<td class="liste_titre">';
                print '&nbsp;';
                print '</td>';
            }

            // Sell price
            if (empty($conf->global->PRODUIT_MULTIPRICES))
            {
                print '<td class="liste_titre">';
                print '&nbsp;';
                print '</td>';
            }

            // Minimum buying Price
            print '<td class="liste_titre">';
            print '&nbsp;';
            print '</td>';

            // Stock
            if (! empty($conf->stock->enabled) && $user->rights->stock->lire && $type != 1)
            {
                print '<td class="liste_titre">';
                print '&nbsp;';
                print '</td>';
            }

            print '<td class="liste_titre">';
            print '&nbsp;';
            print '</td>';

            print '<td class="liste_titre" align="right">';
            print '<input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
            print '<input type="image" class="liste_titre" name="button_removefilter" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/searchclear.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
            print '</td>';
            print '</tr>';


            $product_static=new Product($db);
            $product_fourn =new ProductFournisseur($db);

            $var=true;
            while ($i < min($num,$limit))
            {
                $objp = $db->fetch_object($resql);

                // Multilangs
                if (! empty($conf->global->MAIN_MULTILANGS)) // si l'option est active
                {
                    $sql = "SELECT label";
                    $sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
                    $sql.= " WHERE fk_product=".$objp->rowid;
                    $sql.= " AND lang='". $langs->getDefaultLang() ."'";
                    $sql.= " LIMIT 1";

                    $result = $db->query($sql);
                    if ($result)
                    {
                        $objtp = $db->fetch_object($result);
                        if (! empty($objtp->label)) $objp->label = $objtp->label;
                    }
                }

                $var=!$var;
                print '<tr '.$bc[$var].'>';

                // Ref
                print '<td nowrap="nowrap">';
                $product_static->id = $objp->rowid;
                $product_static->ref = $objp->ref;
                $product_static->type = $objp->fk_product_type;
                print $product_static->getNomUrl(1,'',24);
                print "</td>\n";

                // Label
                print '<td>'.dol_trunc($objp->label,40);
            if($is_declinaison_master) print ' <a href="javascript:quickEditProduct('.$objp->rowid.')" class="quickedit">edit</a></td>';

                // Barcode
                if (! empty($conf->barcode->enabled))
                {
                    print '<td>'.$objp->barcode.'</td>';
                }

                // Date
                print '<td align="center">'.dol_print_date($db->jdate($objp->datem),'day')."</td>\n";

                // Duration
                if (! empty($conf->service->enabled) && $type != 0)
                {
                    print '<td align="center">';
                    if (preg_match('/([0-9]+)y/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationYear");
                    elseif (preg_match('/([0-9]+)m/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationMonth");
                    elseif (preg_match('/([0-9]+)w/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationWeek");
                    elseif (preg_match('/([0-9]+)d/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationDay");
                    else print $objp->duration;
                    print '</td>';
                }

                // Sell price
                if (empty($conf->global->PRODUIT_MULTIPRICES))
                {
                    print '<td align="right">';
                    if ($objp->price_base_type == 'TTC') print price($objp->price_ttc).' '.$langs->trans("TTC");
                    else print price($objp->price).' '.$langs->trans("HT");
                    print '</td>';
                }

                // Better buy price
                print  '<td align="right">';
                if ($objp->minsellprice != '')
                {
                    //print price($objp->minsellprice).' '.$langs->trans("HT");
                    if ($product_fourn->find_min_price_product_fournisseur($objp->rowid) > 0)
                    {
                        if ($product_fourn->product_fourn_price_id > 0)
                        {
                            $htmltext=$product_fourn->display_price_product_fournisseur();
                            if (! empty($conf->fournisseur->enabled) && $user->rights->fournisseur->lire) print $form->textwithpicto(price($product_fourn->fourn_unitprice).' '.$langs->trans("HT"),$htmltext);
                            else print price($product_fourn->fourn_unitprice).' '.$langs->trans("HT");
                        }
                    }
                }
                print '</td>';

                // Show stock
                if (! empty($conf->stock->enabled) && $user->rights->stock->lire && $type != 1)
                {
                    if ($objp->fk_product_type != 1)
                    {
                        $product_static->id = $objp->rowid;
                        $product_static->load_stock();
                        print '<td align="right">';
                        if ($product_static->stock_reel < $objp->seuil_stock_alerte) print img_warning($langs->trans("StockTooLow")).' ';
                        print $product_static->stock_reel;
                        print '</td>';
                    }
                    else
                    {
                        print '<td>&nbsp;</td>';
                    }
                }

                // Status (to buy)
                print '<td align="right" nowrap="nowrap">'.$product_static->LibStatut($objp->tosell,5,0).'</td>';

                // Status (to sell)
                print '<td align="right" nowrap="nowrap">'.$product_static->LibStatut($objp->tobuy,5,1).'</td>';

                print "</tr>\n";
                $i++;
            }

            $db->free($resql);

            print "</table>";
            print '</div>';
            print '</form>';
            
        
    }
    else
    {
        dol_print_error($db);
    }
    
}

function liste_iam_a_declinaison(&$product, $fk_parent_declinaison) {
    global $db, $langs, $conf;
    
    $sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,';
    $sql.= ' p.fk_product_type, p.tms as datem,';
    $sql.= ' p.duration, p.tosell, p.tobuy, p.seuil_stock_alerte,';
    $sql.= ' MIN(pfp.unitprice) as minsellprice';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
    if (! empty($search_categ) || ! empty($catid)) $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX."categorie_product as cp ON p.rowid = cp.fk_product"; // We'll need this table joined to the select in order to filter by categ
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_fournisseur_price as pfp ON p.rowid = pfp.fk_product";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."declinaison as d ON d.fk_declinaison = p.rowid";
    $sql.= ' WHERE p.entity IN ('.getEntity('product', 1).') AND (d.fk_parent='.$fk_parent_declinaison." OR p.rowid=$fk_parent_declinaison)";
    if ($sall)
    {
        $sql.= " AND (p.ref LIKE '%".$db->escape($sall)."%' OR p.label LIKE '%".$db->escape($sall)."%' OR p.description LIKE '%".$db->escape($sall)."%' OR p.note LIKE '%".$db->escape($sall)."%'";
        if (! empty($conf->barcode->enabled))
        {
            $sql.= " OR p.barcode LIKE '%".$db->escape($sall)."%'";
        }
        $sql.= ')';
    }
    // if the type is not 1, we show all products (type = 0,2,3)
    if (dol_strlen($type))
    {
        if ($type == 1) $sql.= " AND p.fk_product_type = '1'";
        else $sql.= " AND p.fk_product_type <> '1'";
    }
    if ($sref)     $sql.= " AND p.ref LIKE '%".$sref."%'";
    if ($sbarcode) $sql.= " AND p.barcode LIKE '%".$sbarcode."%'";
    if ($snom)     $sql.= " AND p.label LIKE '%".$db->escape($snom)."%'";
    if (isset($tosell) && dol_strlen($tosell) > 0) $sql.= " AND p.tosell = ".$db->escape($tosell);
    if (isset($tobuy) && dol_strlen($tobuy) > 0)   $sql.= " AND p.tobuy = ".$db->escape($tobuy);
    if (dol_strlen($canvas) > 0)                    $sql.= " AND p.canvas = '".$db->escape($canvas)."'";
    if ($catid > 0)    $sql.= " AND cp.fk_categorie = ".$catid;
    if ($catid == -2)  $sql.= " AND cp.fk_categorie IS NULL";
    if ($search_categ > 0)   $sql.= " AND cp.fk_categorie = ".$search_categ;
    if ($search_categ == -2) $sql.= " AND cp.fk_categorie IS NULL";
    if ($fourn_id > 0) $sql.= " AND pfp.fk_soc = ".$fourn_id;
    $sql.= " GROUP BY p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,";
    $sql.= " p.fk_product_type, p.tms,";
    $sql.= " p.duration, p.tosell, p.tobuy, p.seuil_stock_alerte";
    //if (GETPOST("toolowstock")) $sql.= " HAVING SUM(s.reel) < p.seuil_stock_alerte";    // Not used yet
    $sql.= $db->order($sortfield,$sortorder);
    $sql.= $db->plimit($limit + 1, $offset);
    
    dol_syslog("sql=".$sql);
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);

        $i = 0;


        $helpurl='';
        if (isset($type))
        {
            if ($type == 0)
            {
                $helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
            }
            else if ($type == 1)
            {
                $helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
            }
        }

        $title=$langs->trans("Declinaison");
        llxHeader('',$title,$helpurl,'');
        
        $head=product_prepare_head($product, $user);
        $titre=$langs->trans("CardProduct".$product->type);
        $picto=($product->type==1?'service':'product');
        dol_fiche_head($head, 'declinaison', $titre, 0, $picto);
        
        $prod = new Product($db);
        $prod->fetch($_REQUEST['fk_product']);      
        
        ?>
            <table class="border" width="100%">
                <tr>
                    <td><?php echo $langs->trans("Ref"); ?></td>
                    <td><?php echo $prod->ref; ?></td>
                </tr>
                <tr>
                    <td><?php echo $langs->trans("Label"); ?></td>
                    <td><?php echo $prod->libelle; ?></td>
                </tr>
            </table><br />      
        <?php
        // Displays product removal confirmation
        if (GETPOST('delprod')) dol_htmloutput_mesg($langs->trans("ProductDeleted",GETPOST('delprod')));

        $param="&fk_product=".$fk_product."&amp;sref=".$sref.($sbarcode?"&amp;sbarcode=".$sbarcode:"")."&amp;snom=".$snom."&amp;sall=".$sall."&amp;tosell=".$tosell."&amp;tobuy=".$tobuy;
        $param.=($fourn_id?"&amp;fourn_id=".$fourn_id:"");
        $param.=($search_categ?"&amp;search_categ=".$search_categ:"");
        $param.=isset($type)?"&amp;type=".$type:"";
        print_barre_liste('', $page, "liste.php", $param, $sortfield, $sortorder,'',$num);

        if (! empty($catid))
        {
            print "<div id='ways'>";
            $c = new Categorie($db);
            $ways = $c->print_all_ways(' &gt; ','product/liste.php');
            print " &gt; ".$ways[0]."<br>\n";
            print "</div><br>";
        }

            print '<form action="liste.php" method="post" name="formulaire">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="list">';
            print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
            print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
            print '<input type="hidden" name="type" value="'.$type.'">';
            print '<input type="hidden" name="fk_product" value="'.$fk_product.'">';

            print '<table id="listDeclinaison" class="liste" width="100%">';

            // Filter on categories
            $moreforfilter='';
            if (! empty($conf->categorie->enabled))
            {
                $moreforfilter.=$langs->trans('Categories'). ': ';
                $moreforfilter.=$htmlother->select_categories(0,$search_categ,'search_categ',1);
                $moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
            }
            if ($moreforfilter)
            {
                print '<tr class="liste_titre">';
                print '<td class="liste_titre" colspan="9">';
                print $moreforfilter;
                print '</td></tr>';
            }

            // Lignes des titres
            print "<tr class=\"liste_titre\">";
            print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "p.ref",$param,"","",$sortfield,$sortorder);
            print_liste_field_titre($langs->trans("Label"), $_SERVER["PHP_SELF"], "p.label",$param,"","",$sortfield,$sortorder);
            if (! empty($conf->barcode->enabled)) print_liste_field_titre($langs->trans("BarCode"), $_SERVER["PHP_SELF"], "p.barcode",$param,'','',$sortfield,$sortorder);
            print_liste_field_titre($langs->trans("DateModification"), $_SERVER["PHP_SELF"], "p.tms",$param,"",'align="center"',$sortfield,$sortorder);
            if (! empty($conf->service->enabled) && $type != 0) print_liste_field_titre($langs->trans("Duration"), $_SERVER["PHP_SELF"], "p.duration",$param,"",'align="center"',$sortfield,$sortorder);
            if (empty($conf->global->PRODUIT_MULTIPRICES)) print_liste_field_titre($langs->trans("SellingPrice"), $_SERVER["PHP_SELF"], "p.price",$param,"",'align="right"',$sortfield,$sortorder);
            print '<td class="liste_titre" align="right">'.$langs->trans("BuyingPriceMinShort").'</td>';
            if (! empty($conf->stock->enabled) && $user->rights->stock->lire && $type != 1) print '<td class="liste_titre" align="right">'.$langs->trans("PhysicalStock").'</td>';
            print_liste_field_titre($langs->trans("Sell"), $_SERVER["PHP_SELF"], "p.tosell",$param,"",'align="right"',$sortfield,$sortorder);
            print_liste_field_titre($langs->trans("Buy"), $_SERVER["PHP_SELF"], "p.tobuy",$param,"",'align="right"',$sortfield,$sortorder);
            print "</tr>\n";

            // Lignes des champs de filtre
            print '<tr class="liste_titre">';
            print '<td class="liste_titre" align="left">';
            print '<input class="flat" type="text" name="sref" size="8" value="'.$sref.'">';
            print '</td>';
            print '<td class="liste_titre" align="left">';
            print '<input class="flat" type="text" name="snom" size="12" value="'.$snom.'">';
            print '</td>';
            if (! empty($conf->barcode->enabled))
            {
                print '<td class="liste_titre">';
                print '<input class="flat" type="text" name="sbarcode" size="6" value="'.$sbarcode.'">';
                print '</td>';
            }
            print '<td class="liste_titre">';
            print '&nbsp;';
            print '</td>';

            // Duration
            if (! empty($conf->service->enabled) && $type != 0)
            {
                print '<td class="liste_titre">';
                print '&nbsp;';
                print '</td>';
            }

            // Sell price
            if (empty($conf->global->PRODUIT_MULTIPRICES))
            {
                print '<td class="liste_titre">';
                print '&nbsp;';
                print '</td>';
            }

            // Minimum buying Price
            print '<td class="liste_titre">';
            print '&nbsp;';
            print '</td>';

            // Stock
            if (! empty($conf->stock->enabled) && $user->rights->stock->lire && $type != 1)
            {
                print '<td class="liste_titre">';
                print '&nbsp;';
                print '</td>';
            }

            print '<td class="liste_titre">';
            print '&nbsp;';
            print '</td>';

            print '<td class="liste_titre" align="right">';
            print '<input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
            print '<input type="image" class="liste_titre" name="button_removefilter" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/searchclear.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
            print '</td>';
            print '</tr>';


            $product_static=new Product($db);
            $product_fourn =new ProductFournisseur($db);

            $var=true;
            while ($i < min($num,$limit))
            {
                $objp = $db->fetch_object($resql);

                // Multilangs
                if (! empty($conf->global->MAIN_MULTILANGS)) // si l'option est active
                {
                    $sql = "SELECT label";
                    $sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
                    $sql.= " WHERE fk_product=".$objp->rowid;
                    $sql.= " AND lang='". $langs->getDefaultLang() ."'";
                    $sql.= " LIMIT 1";

                    $result = $db->query($sql);
                    if ($result)
                    {
                        $objtp = $db->fetch_object($result);
                        if (! empty($objtp->label)) $objp->label = $objtp->label;
                    }
                }

                $var=!$var;
                print '<tr '.$bc[$var].'>';

                // Ref
                print '<td nowrap="nowrap">';
                $product_static->id = $objp->rowid;
                $product_static->ref = $objp->ref;
                $product_static->type = $objp->fk_product_type;
                print $product_static->getNomUrl(1,'',24);
                print "</td>\n";

                // Label
                print '<td>'.dol_trunc($objp->label,40);
            if($is_declinaison_master) print ' <a href="javascript:quickEditProduct('.$objp->rowid.')" class="quickedit">edit</a></td>';

                // Barcode
                if (! empty($conf->barcode->enabled))
                {
                    print '<td>'.$objp->barcode.'</td>';
                }

                // Date
                print '<td align="center">'.dol_print_date($db->jdate($objp->datem),'day')."</td>\n";

                // Duration
                if (! empty($conf->service->enabled) && $type != 0)
                {
                    print '<td align="center">';
                    if (preg_match('/([0-9]+)y/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationYear");
                    elseif (preg_match('/([0-9]+)m/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationMonth");
                    elseif (preg_match('/([0-9]+)w/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationWeek");
                    elseif (preg_match('/([0-9]+)d/i',$objp->duration,$regs)) print $regs[1].' '.$langs->trans("DurationDay");
                    else print $objp->duration;
                    print '</td>';
                }

                // Sell price
                if (empty($conf->global->PRODUIT_MULTIPRICES))
                {
                    print '<td align="right">';
                    if ($objp->price_base_type == 'TTC') print price($objp->price_ttc).' '.$langs->trans("TTC");
                    else print price($objp->price).' '.$langs->trans("HT");
                    print '</td>';
                }

                // Better buy price
                print  '<td align="right">';
                if ($objp->minsellprice != '')
                {
                    //print price($objp->minsellprice).' '.$langs->trans("HT");
                    if ($product_fourn->find_min_price_product_fournisseur($objp->rowid) > 0)
                    {
                        if ($product_fourn->product_fourn_price_id > 0)
                        {
                            $htmltext=$product_fourn->display_price_product_fournisseur();
                            if (! empty($conf->fournisseur->enabled) && $user->rights->fournisseur->lire) print $form->textwithpicto(price($product_fourn->fourn_unitprice).' '.$langs->trans("HT"),$htmltext);
                            else print price($product_fourn->fourn_unitprice).' '.$langs->trans("HT");
                        }
                    }
                }
                print '</td>';

                // Show stock
                if (! empty($conf->stock->enabled) && $user->rights->stock->lire && $type != 1)
                {
                    if ($objp->fk_product_type != 1)
                    {
                        $product_static->id = $objp->rowid;
                        $product_static->load_stock();
                        print '<td align="right">';
                        if ($product_static->stock_reel < $objp->seuil_stock_alerte) print img_warning($langs->trans("StockTooLow")).' ';
                        print $product_static->stock_reel;
                        print '</td>';
                    }
                    else
                    {
                        print '<td>&nbsp;</td>';
                    }
                }

                // Status (to buy)
                print '<td align="right" nowrap="nowrap">'.$product_static->LibStatut($objp->tosell,5,0).'</td>';

                // Status (to sell)
                print '<td align="right" nowrap="nowrap">'.$product_static->LibStatut($objp->tobuy,5,1).'</td>';

                print "</tr>\n";
                $i++;
            }

            $db->free($resql);

            print "</table>";
            print '</div>';
            print '</form>';
            
        
    }
    else
    {
        dol_print_error($db);
    }
    
}
