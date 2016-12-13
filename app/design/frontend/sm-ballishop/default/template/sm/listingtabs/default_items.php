<?php
/*------------------------------------------------------------------------
	# SM Listing Tabs - Version 2.0.1
	# Copyright (c) 2014 YouTech Company. All Rights Reserved.
	# @license - Copyrighted Commercial Software
	# Author: YouTech Company
	# Websites: http://www.magentech.com
   -------------------------------------------------------------------------*/
$config = Mage::helper('ballishop/config');
$effect_style = $config->getProductListing('effect_style');
$display_nav = $config->getProductListing('show_nav');
$display_dot = $config->getProductListing('show_dot');
$limit = $config->getProductListing('slide_limit');

$helper = Mage::helper('listingtabs/data');

$loadType = $this->_getConfig('show_loadmore_slider');

if ($this->_isAjax()) {
    $type_filter = $this->_getConfig('filter_type');
    switch ($type_filter) {
        case 'categories':
            $catid = $this->getRequest()->getPost('categoryid');
            $catid = $this->getRequest()->getPost('categoryid');
            $child_items = $this->_getProductInfor($catid);
            break;
        case 'fieldproducts':
            $field_order = $this->getRequest()->getPost('categoryid');
            $catid = $this->_getCatIds();
            $child_items = $this->_getProductInfor($catid, $field_order);
            break;
    }
}

if (!empty($child_items)) {
    $k = $this->getRequest()->getPost('ajax_listingtags_start', 0);
    foreach ($child_items as $_product) {
        $k++; ?>
        <div class="ltabs-item new-ltabs-item item">
            <div class="item-inner">
				
				<?php if ($_product->_image) {?>
				<div class="box-image">
					<?php if($effect_style == 'default'){?>
						<div class="effect-default">
							<a class="rspl-image"
							   href="<?php echo $_product->link ?>">
								<img title="<?php echo $_product->title; ?>"
									 alt="<?php echo $_product->title; ?>"
									 src="<?php echo $_product->_image; ?>"/>
							</a>
						</div>
						<?php } else if($effect_style == 'thumbs'){?>
						<div class="effect-thumbs">
							<a href="<?php echo $_product->link ?>" class="product-image">
								<img title="<?php echo $_product->title; ?>"
									 alt="<?php echo $_product->title; ?>"
									 src="<?php echo $_product->_image; ?>"/>
											 
								<?php if($_product->getThumbnail() != $_product->getSmallImage()) { ?> 
									<img class="second-image" src="<?php echo $this->helper('catalog/image')->init($_product, 'thumbnail')->resize(270, 360); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" />
								<?php } ?>
							</a>
						</div>
					<?php } ?>
					
					
					<div class="actions">
						<?php if ((int)$this->_getConfig('product_addcart_display', 1)) { ?>
							<?php if ($_product->isSaleable()) { ?>
								<button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="btn-action btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')">
									<span>
										<span> <?php echo $this->__('Add to Cart') ?> </span>
									</span>
								</button>
							<?php } else { ?>
								<p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
							<?php } ?>
						<?php } ?>
						
						<?php if ((int)$this->_getConfig('product_addwishlist_display', 1) || (int)$this->_getConfig('product_addcompare_display', 1)) { ?>
							<ul class="add-to-links">
								<?php if ((int)$this->_getConfig('product_addwishlist_display', 1)) { ?>
									<?php if ($this->helper('wishlist')->isAllow()) { ?>
										<li>
											<a title="<?php echo $this->__('Wishlist') ?>" href="<?php echo $this->helper('wishlist')->getAddUrl($_product) ?>" class="btn-action link-wishlist">
												<span><?php echo $this->__('Wishlist') ?></span>
											</a>
										</li>
									<?php } ?>
								<?php } ?>
								<?php if ((int)$this->_getConfig('product_addcompare_display', 1)) { ?>
									<?php if ($_compareUrl = $this->getAddToCompareUrl($_product)) { ?>
										<li>
											<a title="<?php echo $this->__('Compare') ?>" href="<?php echo $_compareUrl ?>" class="btn-action link-compare">
												<span><?php echo $this->__('Compare') ?></span>
											</a>
										</li>
									<?php } ?>
								<?php } ?>
							</ul>
						<?php } ?>
						
					</div>
					
								<!--LABEL PRODUCT-->
								<?php
                                $id_product = Mage::getModel('catalog/product')->load($_product->getId());
                                $specialprice = $id_product->getSpecialPrice();
                                $specialPriceFromDate = $id_product->getSpecialFromDate();
                                $specialPriceToDate = $id_product->getSpecialToDate();
                                $today = time();

                                if ($specialprice) {
                                    if ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate)) { ?>
                                        <div class="label-product label-sale">
											<span class="sale-product-icon">
												<?php echo $this->__('Sale'); ?>
											</span>
                                        </div>
                                    <?php }
                                }?>


                                <?php
                                $now = date("Y-m-d");
                                $newsFrom = substr($_product->getData('news_from_date'), 0, 10);
                                $newsTo = substr($_product->getData('news_to_date'), 0, 10);
                                if ($newsTo != '' || $newsFrom != '') {
                                    if (($newsTo != '' && $newsFrom != '' && $now >= $newsFrom && $now <= $newsTo) || ($newsTo == '' && $now >= $newsFrom) || ($newsFrom == '' && $now <= $newsTo)) {?>
                                            <div class="label-product label-new">
												<span class="new-product-icon"><?php echo $this->__('New'); ?></span>
											</div>
                                    <?php }
                                } ?>
								
								<!--END LABEL PRODUCT-->
					
				</div>
				<?php } ?>
				
				<div class="box-info">
					<?php if ($this->_getConfig('product_title_display', 1) == 1) {?>
					<h2 class="product-name">
						<a href="<?php echo $_product->link ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')) ?> title="<?php echo $_product->title ?>">
                            <?php echo $helper->truncate($_product->title, $this->_getConfig('product_title_maxlength', 25)); ?>
                        </a>
					</h2>
					<?php } ?>
					
					<?php if ((int)$this->_getConfig('product_reviews_count')) { ?>
						<?php echo $this->getReviewsSummaryHtml($_product, "short", true); ?>
					<?php } ?>
					
					<?php if ((int)$this->_getConfig('product_price_display', 1)) { ?>
						<?php echo $this->getPriceHtml($_product, true); ?>
					<?php } ?>
					
					<?php if ($this->_getConfig('product_description_display', 1) == 1 && $helper->_trimEncode($_product->_description) != '') { ?>
						<div class="item-desc">
							<?php echo $_product->_description; ?>
						</div>
					<?php } ?>

					<?php if ($this->_getConfig('product_date_display', 1) == 1) { ?>
						<div class="created-date ">
							<?php echo date("d F Y", strtotime($_product->created_at)); ?>
						</div>
					<?php } ?>
					
					

					<?php if ((int)$this->_getConfig('product_readmore_display', 1)) { ?>
						<div class="item-readmore">
							<a href="<?php echo $_product->link; ?>"
							   title="<?php echo $_product->title ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')); ?> >
								<?php echo $this->_getConfig('product_readmore_text', 'Detail'); ?>
							</a>
						</div>
					<?php } ?>
					<div class="other-infor">
						<?php if ($this->_getConfig('product_hits_display')) { ?>
							<div class="hits"><?php echo 'Read'; ?>
								<?php
								if ($_product->num_view_counts > 1) {
									echo $_product->num_view_counts . ' times';
								} else {
									echo $_product->num_view_counts . ' time';
								}?>
							</div>
						<?php } ?>
					</div>
					
				</div>

            </div>
        </div>
        <?php $clear = 'clr1';
        if ($k % 2 == 0) $clear .= ' clr2';
        if ($k % 3 == 0) $clear .= ' clr3';
        if ($k % 4 == 0) $clear .= ' clr4';
        if ($k % 5 == 0) $clear .= ' clr5';
        if ($k % 6 == 0) $clear .= ' clr6';
        ?>
        <?php if( $this->_getConfig('show_loadmore_slider') == 'loadmore'){ ?>
            <div class="<?php echo $clear; ?>"></div>
        <?php } ?>
    <?php
    }
}?>
