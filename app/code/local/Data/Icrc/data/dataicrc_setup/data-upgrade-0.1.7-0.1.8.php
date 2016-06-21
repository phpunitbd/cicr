<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();
Mage::register('isSecureArea', true);

$pppen = '<h1>ICRC pricing policy</h1>
<p>In accordance with its mission statement, the ICRC is committed to promoting and strengthening humanitarian law and universal humanitarian principles. One way in which it does this is by making its resources as widely available, and inexpensive, as possible. However, because it is accountable to its donors, the ICRC has also to try to recover its production costs.</p>
<p>If you reside in one of the countries listed below, you qualify for a 20 per cent discount on your ICRC publication or film purchase (excluding shipping).The prices listed on this web site are before any discounts. When you order online, the system will show your discounted price during the checkout process, at the order summary screen, right before you place your order.</p>
<p>Countries eligible for 20 per cent discount:<br />
Afghanistan, Angola, Armenia, Bangladesh, Belize, Benin, Bhutan, Bolivia, 
Burkina Faso, Burundi, Cambodia, Cameroon, Cape Verde, 
Central African Republic, Chad, Comoros, Congo, Dem. Rep., Congo, Rep., 
Côte d\'Ivoire, Djibouti, Egypt, El Salvador, Eritrea, Ethiopia, Fiji, Gambia, 
Georgia, Ghana, Guatemala, Guinea, Guinea-Bissau, Guyana, Haiti, Honduras, 
India, Indonesia, Iraq, Kenya, Kiribati, Korea, Dem. Rep., Kyrgyz Republic, 
Lao PDR, Lesotho, Liberia, Madagascar, Malawi, Mali, Marshall Islands, 
Mauritania, Micronesia Fed. Sts., Moldova, Mongolia, Morocco, Mozambique, 
Myanmar, Nepal, Nicaragua, Niger, Nigeria, Pakistan, Papua New Guinea, 
Paraguay, Philippines, Rwanda, Samoa, São Tomé and Príncipe, Senegal, 
Sierra Leone, Solomon Islands, Somalia, South Sudan, Sri Lanka, Sudan, 
Swaziland, Syrian Arab Republic, Tajikistan, Tanzania, Timor-Leste, Togo, 
Tonga, Turkmenistan, Tuvalu, Uganda, Ukraine, Uzbekistan, Vanuatu, Vietnam, 
Yemen, Zambia, Zimbabwe</p>
<p>The following regions also qualify: Kosovo (UN Security Council Resolution 1244) and the occupied and Palestinian Territories.</p>
<p>Furthermore, the ICRC gives a discount of 20 per cent on the price of its publications to the following entities: the International Federation of Red Cross and Red Crescent Societies, Red Cross and Red Crescent Museum (Geneva), National Red Cross and Red Crescent Societies and ICRC staff for their personal use.</p>
<p>Several general resources on international humanitarian law, the ICRC or the International Red Cross and Red Crescent Movement, are offered free of charge; clients are, however, billed postage for parcels weighing over 1.5 kg.</p>
<p>Prices in this catalogue are given in Swiss francs (CHF), EUROS or US dollars and weights in kilograms (kg) and grams (g). </p>
<p>In addition to the 20 per cent discount for certain countries and audiences, there is a price reduction for bulk orders on a sliding scale.</p>
<ul><li>1-9 0</li>
<li>10-19 10%</li>
<li>20-49 15%</li> 
<li>50-99 20%</li> 
<li>100-499 25%</li>
<li>500-999 30%</li>
<li>1,000-4,999 40%</li>
<li>5,000-9,999 50%</li> 
<li>More than 9,999 60%</li></ul>';

Mage::helper('data_icrc/update')->updateCmsPage('pricing-policy', array($en, $int), $pppen);

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();


