<?php

function wc_product_data()
{
    $arrProducts = array();
    $products = wc_get_products(array('status' => 'publish', 'limit' => -1));


    if (!empty($products)) {
        foreach ($products as $product) {

            array_push($arrProducts, array(
                'id' => $product->get_id(),
                'title' => $product->get_title(),
                'content' => $product->get_short_description(),
                'full_content' => $product->get_description(),
                'product_link' => $product->get_permalink(),
                'regular_price' => $product->get_regular_price(),
                'sale_price' => $product->get_sale_price(),
                'uniq_id' => $product->get_sku(),
                'tag' => wc_get_product_tag_list($product->get_id()),
                'categories' => wc_get_product_category_list($product->get_id()),
                'image_url' => wp_get_attachment_url($product->get_image_id())
            ));
        }
    } else {
        echo '<option value="" disabled>No products</option>';
    }
    return $arrProducts;
}
?>
<?php

function productSelectBox($arrProducts)
{

    foreach ($arrProducts as $product) {

?>
        <option <?php if (isset($_GET['id'])) {
                    if ($product['id'] == $_GET['id']) {
                        echo 'selected';
                    }
                } ?> value='<?php echo $product['id'] ?>'>
            <p>ID: <?php echo $product['id'] ?></p>
            <p>Product Name: <?php echo $product['title'] ?></p>
            <p>Price: <?php echo $product['regular_price'] ?></p>
        </option>
<?php }
}
?>