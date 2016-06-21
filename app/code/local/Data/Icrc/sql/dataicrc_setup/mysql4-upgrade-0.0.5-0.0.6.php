<?php

$this->startSetup();

function createBlock($title, $store, $id, $cnt = null)
{
  try {
    $staticBlock = array(
                'title' => $title,
                'identifier' => $id,
                'content' => $cnt ? $cnt : 'Sample data for block '.$title,
                'is_active' => 1,
                'stores' => array($store)
                );
 
    Mage::getModel('cms/block')->setData($staticBlock)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}
$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

$image = array(1 => 'carroussel-assistance', 2 => 'carroussel-healcare', 3 => 'carroussel-nouveaux-produits', 4 => 'carroussel-produit-mois');
$fr_txt = array();
$en_txt = array(
1 => '<table border="0" align="center">
<tbody>
<tr>
<td>
<div class="block-text">
<p style="font-weight: bold; color: #267ebd; font-size: 25px; margin-left: 12px; margin-right: 12px;">THEMA: "Assistance"</p>
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px;">The aim of ICRC\'s assistance programmes is to preserve life and restore the dignity of individuals and communities affected by armed conflict or other situations of violence.</p>
</div>
<img class="product-home-separator" style="margin-top: -5px;" src="{{media url="wysiwyg/carroussel-home-separateur.png"}}" alt="" />
<p style="font-weight: bold; color: black; font-size: 12px; text-align: center; margin-top: -15px;">Related products :</p>
{{block type="core/template" template="catalog/product/home.phtml" product="S2007.02-03,4037,0962" size=100 max_title_len=70}}</td>
<td><img title="home" src="{{media url="wysiwyg/carroussel-assistance.jpg"}}" alt="home" /></td>
</tr>
</tbody>
</table>',
2 => '<table border="0" align="center">
<tbody>
<tr>
<td>
<div class="block-text">
<p style="font-weight: bold; color: #267ebd; font-size: 25px; margin-left: 12px; margin-right: 12px;">HEALTH CARE IN DANGER</p>
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px;">The Health Care in Danger campaigns is an ICRC-led, Red Cross and Red Crescent Movement-wide initiative that aims to address the widespread and severe impact of illegal and sometimes violent acts that obstruct the delivery of health care, damage or destroy facilities and vehicules, and injure or kill health-care workers and patients, in armed conflicts and other emergencies.</p>
</div>
<img class="product-home-separator" style="margin-top: -5px;" src="{{media url="wysiwyg/carroussel-home-separateur.png"}}" alt="" />
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px; text-align: center;">Related products:</p>
{{block type="core/template" template="catalog/product/home.phtml" product="4072,4074,1127" size=100 max_title_len=70}}</td>
<td><img title="home" src="{{media url="wysiwyg/carroussel-healcare.jpg"}}" alt="home" /></td>
</tr>
</tbody>
</table>',
3 => '<table border="0" align="center">
<tbody>
<tr>
<td>
<div class="block-text">
<p style="font-weight: bold; color: #267ebd; font-size: 25px; margin-left: 12px; margin-right: 12px;">NEW PRODUCTS</p>
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px;">The ICRC produces a wide range of communication materails in the form of publications, films, and multimedia to promote international humanitarian law, to increase awareness of dangers such as landmines or to outline activities in specific countries.</p>
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px;">Most products can be ordered online and many of our publications an films can be downloaded free of charge.</p>
</div>
<img class="product-home-separator" style="margin-top: -5px;" src="{{media url="wysiwyg/carroussel-home-separateur.png"}}" alt="" />
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px; text-align: center;">Please find below our last productions:</p>
{{block type="core/template" template="catalog/product/home.phtml" product="S2007.02-03,4037,0962" size=100 max_title_len=70}}</td>
<td><img title="home" src="{{media url="wysiwyg/carroussel-nouveaux-produits.jpg"}}" alt="home" /></td>
</tr>
</tbody>
</table>',
4 => '<table border="0" align="center">
<tbody>
<tr>
<td>
<div class="block-text">
<p style="font-weight: bold; color: #267ebd; font-size: 25px; margin-left: 12px; margin-right: 12px;">PRODUCT OF THE MONTH</p>
<p style="font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px;">How does law protect in war? Cases, documents and teaching materials on contemporary practice in international humanitarian law.</p>
<p style="font-size: 13px; color: black; margin-left: 12px; margin-top: -10px;">A selection of nearly three hundred case studies provides university professors, practitioners and students with the most up-to-date and comprehensive selection of documents on international humanitarian law (IHL) available. The publication presents the most fundamental and contemporary legal issues in armed conflict, and a series of course outlines for professors interested in setting up courses on IHL or in introducing its study.</p>
</div>
<img class="product-home-separator" style="margin-top: -5px;" src="{{media url="wysiwyg/carroussel-home-separateur.png"}}" alt="" />{{block type="core/template" template="catalog/product/home.phtml" product="0739" size=100 max_title_len=70}}</td>
<td><img title="home" src="{{media url="wysiwyg/carroussel-produit-mois.jpg"}}" alt="home" /></td>
</tr>
</tbody>
</table>');

$liber = array('',
  'Gallia est omnis divisa in partes tres, quarum unam incolunt Belgae, aliam Aquitani, tertiam qui ipsorum lingua Celtae, nostra Galli appellantur. Hi omnes lingua, institutis, legibus inter se differunt. Gallos ab Aquitanis Garumna flumen, a Belgis Matrona et Sequana dividit. Horum omnium fortissimi sunt Belgae, propterea quod a cultu atque humanitate provinciae longissime absunt, minimeque ad eos mercatores saepe commeant atque ea quae ad effeminandos animos pertinent important, proximique sunt Germanis, qui trans Rhenum incolunt, quibuscum continenter bellum gerunt. Qua de causa Helvetii quoque reliquos Gallos virtute praecedunt, quod fere cotidianis proeliis cum Germanis contendunt, cum aut suis finibus eos prohibent aut ipsi in eorum finibus bellum gerunt. Eorum una pars, quam Gallos obtinere dictum est, initium capit a flumine Rhodano, continetur Garumna flumine, Oceano, finibus Belgarum, attingit etiam ab Sequanis et Helvetiis flumen Rhenum, vergit ad septentriones. Belgae ab extremis Galliae finibus oriuntur, pertinent ad inferiorem partem fluminis Rheni, spectant in septentrionem et orientem solem. Aquitania a Garumna flumine ad Pyrenaeos montes et eam partem Oceani quae est ad Hispaniam pertinet; spectat inter occasum solis et septentriones.',
  'Apud Helvetios longe nobilissimus fuit et ditissimus Orgetorix. Is M. Messala, [et P.] M. Pisone consulibus regni cupiditate inductus coniurationem nobilitatis fecit et civitati persuasit ut de finibus suis cum omnibus copiis exirent: perfacile esse, cum virtute omnibus praestarent, totius Galliae imperio potiri. Id hoc facilius iis persuasit, quod undique loci natura Helvetii continentur: una ex parte flumine Rheno latissimo atque altissimo, qui agrum Helvetium a Germanis dividit; altera ex parte monte Iura altissimo, qui est inter Sequanos et Helvetios; tertia lacu Lemanno et flumine Rhodano, qui provinciam nostram ab Helvetiis dividit. His rebus fiebat ut et minus late vagarentur et minus facile finitimis bellum inferre possent; qua ex parte homines bellandi cupidi magno dolore adficiebantur. Pro multitudine autem hominum et pro gloria belli atque fortitudinis angustos se fines habere arbitrabantur, qui in longitudinem milia passuum CCXL, in latitudinem CLXXX patebant.',
  'His rebus adducti et auctoritate Orgetorigis permoti constituerunt ea quae ad proficiscendum pertinerent comparare, iumentorum et carrorum quam maximum numerum coemere, sementes quam maximas facere, ut in itinere copia frumenti suppeteret, cum proximis civitatibus pacem et amicitiam confirmare. Ad eas res conficiendas biennium sibi satis esse duxerunt; in tertium annum profectionem lege confirmant. Ad eas res conficiendas Orgetorix deligitur. Is sibi legationem ad civitates suscipit. In eo itinere persuadet Castico, Catamantaloedis filio, Sequano, cuius pater regnum in Sequanis multos annos obtinuerat et a senatu populi Romani amicus appellatus erat, ut regnum in civitate sua occuparet, quod pater ante habuerit; itemque Dumnorigi Haeduo, fratri Diviciaci, qui eo tempore principatum in civitate obtinebat ac maxime plebi acceptus erat, ut idem conaretur persuadet eique filiam suam in matrimonium dat. Perfacile factu esse illis probat conata perficere, propterea quod ipse suae civitatis imperium obtenturus esset: non esse dubium quin totius Galliae plurimum Helvetii possent; se suis copiis suoque exercitu illis regna conciliaturum confirmat. Hac oratione adducti inter se fidem et ius iurandum dant et regno occupato per tres potentissimos ac firmissimos populos totius Galliae sese potiri posse sperant.',
  'Ea res est Helvetiis per indicium enuntiata. Moribus suis Orgetoricem ex vinculis causam dicere coegerunt; damnatum poenam sequi oportebat, ut igni cremaretur. Die constituta causae dictionis Orgetorix ad iudicium omnem suam familiam, ad hominum milia decem, undique coegit, et omnes clientes obaeratosque suos, quorum magnum numerum habebat, eodem conduxit; per eos ne causam diceret se eripuit. Cum civitas ob eam rem incitata armis ius suum exequi conaretur multitudinemque hominum ex agris magistratus cogerent, Orgetorix mortuus est; neque abest suspicio, ut Helvetii arbitrantur, quin ipse sibi mortem consciverit.',
);

for ($i = 1; $i <= 4; $i++) {
  $slide_cnt = "<table border=\"0\" align=\"center\">
<tbody>
<tr>
<td class=\"block-text\"><div class=\"block-text\">
<p style=\"font-weight: bold; color: #267ebd; font-size: 25px; margin-left: 12px; margin-right: 12px;\">TITLE</p>
<p style=\"font-size: 13px; font-weight: bold; color: black; margin-left: 12px; margin-top: -10px;\">$liber[$i]</p>
</div>
</td>
<td><img title=\"home\" src=\"{{media url=\"wysiwyg/$image[$i].jpg\"}}\" alt=\"home\" /></td>
</tr>
</tbody>
</table>";
  if (array_key_exists($i, $en_txt))
    $slide_cnt_en = $en_txt[$i];
  else 
    $slide_cnt_en = $slide_cnt;
  if (array_key_exists($i, $fr_txt))
    $slide_cnt_fr = $fr_txt[$i];
  else 
    $slide_cnt_fr = $slide_cnt;
  createBlock("Homepage Slide #$i for EN", $en, "index_slide_$i", $slide_cnt_en);
  createBlock("Homepage Slide #$i for FR", $fr, "index_slide_$i", $slide_cnt_fr);
$promo_cnt = "<table class=\"promo-block\" border=\"0\">
<tbody>
<tr>
<td style=\"text-align: center;\">&nbsp;<img title=\"img\" src=\"{{media url=\"wysiwyg/publiref.jpg\"}}\" alt=\"img\" /></td>
</tr>
<tr>
<td style=\"text-align: center;\" class=\"promo$i\">Caesari cum id nuntiatum esset, eos per provinciam nostram iter facere conari, maturat ab urbe proficisci et quam maximis potest itineribus in Galliam ulteriorem contendit et ad Genavam pervenit.</td>
</tr>
</tbody>
</table>";
  createBlock("Promo Block #$i for EN", $en, "index_foot_$i", $promo_cnt);
  createBlock("Promo Block #$i for FR", $fr, "index_foot_$i", $promo_cnt);
}

$this->endSetup();

