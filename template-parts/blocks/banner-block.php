<div class="block_banner position-relative">
    <?php if(!empty($args['fields']['banner_link'])): ?>
        <a href="<?php echo $args['fields']['banner_link']; ?>">
    <?php endif; ?>
        <div class="block_banner__image">
            <?php echo wp_get_attachment_image($args['fields']['banner_image'], 'full'); ?>
        </div>
        <?php if(!empty($args['fields']['banner_title'])): ?>
            <h1 class="block_banner__title"><?php echo $args['fields']['banner_title']; ?></h1>
        <?php endif; ?>
        <!-- <?php if(!empty($args['fields']['banner_description'])): ?>
            <div class="block_banner__description"><?php echo $args['fields']['banner_description']; ?></div>
        <?php endif; ?> -->
    <?php if(!empty($args['fields']['banner_link'])): ?>
        </a>
    <?php endif; ?>
</div>