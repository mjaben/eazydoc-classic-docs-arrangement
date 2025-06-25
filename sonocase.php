//Sonocase

add_action('admin_menu', 'sonocases_menu');

function sonocases_menu() {
    add_menu_page(
        'Sonocases', // Page title
        'Sonocases', // Menu title
        'manage_options', // Capability
        'sonocases', // Menu slug
        'display_sonocases', // Function to display content
        'dashicons-media-document', // Icon
        6 // Position
    );
}

// Function to retrieve Level 4 (offspring) documents
function sonocases_get_offspring_docs($search_term = '') {
    $args = array(
        'post_type'      => 'docs', // EasyDocs post type
        'post_status'    => 'publish', // Only published posts
        'posts_per_page' => -1, // Get all posts for now
        's'              => $search_term, // Allow search term filtering
    );

    $all_docs = get_posts($args);
    $offspring_docs = array(); // Array to store Level 4 documents

    foreach ($all_docs as $doc) {
        $current_level = count(get_post_ancestors($doc->ID)); // Get the hierarchy level
        if ($current_level === 3) { // Level 3 ancestors + current = Level 4
            $offspring_docs[] = $doc; // Add to offspring array
        }
    }

    return $offspring_docs;
}

// Admin page to display Level 4 Offspring Documents
function display_sonocases() {
    // Retrieve search term
    $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    // Fetch Level 4 documents
    $offspring_docs = sonocases_get_offspring_docs($search_term);

    echo '<div class="wrap"><h1>Sonocases - Level 4 Documents</h1>';

    // Filters form (Search Box)
    echo '<form method="get" action="">
            <input type="hidden" name="page" value="sonocases" />
            <input type="text" name="s" value="' . esc_attr($search_term) . '" placeholder="Search Level 4 documents..." />
            <input type="submit" value="Search" class="button button-primary" />
            <a href="' . admin_url('admin.php?page=sonocases') . '" class="button">Reset Search</a>
          </form>';

    // Display total cases count
    $total_cases = count($offspring_docs);
    echo '<p><strong>Total Level 4 Documents:</strong> ' . $total_cases . '</p>';

    // Check if there are any documents
    if (!empty($offspring_docs)) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>
                <tr>
                    <th scope="col" class="manage-column">S/N</th>
                    <th scope="col" class="manage-column">Title</th>
                    <th scope="col" class="manage-column">Parent</th>
                    <th scope="col" class="manage-column">Date</th>
                </tr>
              </thead>';
        echo '<tbody>';

        $count = 1; // Initialize numbering

        foreach ($offspring_docs as $doc) {
            // Get the parent post
            $parent_id = wp_get_post_parent_id($doc->ID);
            $parent_title = $parent_id ? get_the_title($parent_id) : 'No Parent';

            echo '<tr>';
            echo '<td>' . esc_html($count++) . '</td>'; // Display numbering
            echo '<td><a href="' . esc_url(get_permalink($doc->ID)) . '">' . esc_html($doc->post_title) . '</a></td>'; // Display title with link
            echo '<td>' . esc_html($parent_title) . '</td>'; // Display parent title
            echo '<td>' . get_the_date('', $doc->ID) . '</td>'; // Display the date
            echo '</tr>';
        }

        echo '</tbody></table></div>';
    } else {
        echo '<p>No Level 4 documents found matching the criteria.</p>';
    }
}


