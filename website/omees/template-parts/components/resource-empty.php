<?php
$title = $args['title'] ?? '';
$message = $args['message'] ?? '';
$classes = trim('resourceEmpty ' . ($args['classes'] ?? ''));
$heading_class = $args['heading_class'] ?? '';
$heading_tag = $args['heading_tag'] ?? 'h6';

if (!in_array($heading_tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
    $heading_tag = 'h6';
}
?>

<div class="<?php echo esc_attr($classes); ?>">
    <div class="resourceEmpty__icon mb-2">
        <i class="ri-search-eye-line ri-lg"></i>
    </div>

    <<?php echo $heading_tag; ?><?php if ($heading_class !== '') : ?> class="<?php echo esc_attr($heading_class); ?>"<?php endif; ?>><?php echo esc_html($title); ?></<?php echo $heading_tag; ?>>

    <p><?php echo esc_html($message); ?></p>
</div>
