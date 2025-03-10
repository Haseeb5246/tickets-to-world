<?php
require_once '../private/config.php';

header('Content-Type: application/json');

function getLocations($search = '') {
    global $conn;
    
    if ($search) {
        $search = '%' . $conn->real_escape_string($search) . '%';
        $sql = "SELECT DISTINCT location FROM (
                    SELECT DISTINCT location FROM (
                        SELECT TRIM(REGEXP_REPLACE(from_location, '\\s+', ' ')) as location
                        FROM available_flights 
                        WHERE LOWER(TRIM(from_location)) LIKE LOWER(?)
                        UNION 
                        SELECT TRIM(REGEXP_REPLACE(to_location, '\\s+', ' '))
                        FROM available_flights 
                        WHERE LOWER(TRIM(to_location)) LIKE LOWER(?)
                    ) as all_locs
                    GROUP BY LOWER(TRIM(location))
                ) as unique_locs 
                ORDER BY location ASC 
                LIMIT 8";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $search, $search);
    } else {
        $sql = "SELECT DISTINCT location, total_count FROM (
                    SELECT location, SUM(count) as total_count FROM (
                        SELECT TRIM(REGEXP_REPLACE(from_location, '\\s+', ' ')) as location, 
                               COUNT(*) as count
                        FROM available_flights 
                        GROUP BY LOWER(TRIM(from_location))
                        UNION ALL
                        SELECT TRIM(REGEXP_REPLACE(to_location, '\\s+', ' ')), 
                               COUNT(*)
                        FROM available_flights 
                        GROUP BY LOWER(TRIM(to_location))
                    ) as all_counts
                    GROUP BY LOWER(TRIM(location))
                ) as unique_counts 
                ORDER BY total_count DESC, location ASC
                LIMIT 8";
        
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
    }
    
    $stmt->close();
    return array_values(array_unique($locations));
}

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$locations = getLocations($searchTerm);

echo json_encode($locations);
?>
