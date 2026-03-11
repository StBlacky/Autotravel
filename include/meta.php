<?php
/**
 * Универсальная генерация метатегов для Autotravel.ru
 * Версия: ноябрь 2025
 *
 * Поддерживает типы:
 *   - area      (области)
 *   - town      (города)
 *   - otklik    (объекты/достопримечательности)
 *
 * Используются только существующие поля БД:
 *   areas:  id, name, name_r, name_v, meta_title, meta_description
 *   towns:  id, name, namein, namearound, nameto, area, meta_title, meta_description
 *   foto:   file  (для og:image)
 */

function build_meta($type, $data = [], $sitename = 'autotravel.ru') {
    $title = '';
    $desc  = '';
    $ogimg = '';

    switch ($type) {

        // --- РЕГИОН (areas.php) ---
        case 'area':
            $name        = trim($data['name']);
            $name_r      = trim($data['name_r']);
            $name_v      = trim($data['name_v']);
            $meta_title  = trim($data['meta_title'] ?? '');
            $meta_desc   = trim($data['meta_description'] ?? '');

            // Подбираем предлог: "на" только для отдельных регионов
            $prep_v = 'в ';
            if (preg_match('/^(Камчат|Чукот|Сахалин|Алтай)/ui', $name_v)) {
                $prep_v = 'на ';
            }

            // Если есть кастомные поля — используем их
            if ($meta_title) {
                $title = $meta_title;
            } else {
                $title = "Автопутешествия по {$name_r} — города, маршруты и достопримечательности | Autotravel.ru";
            }

            if ($meta_desc) {
                $desc = $meta_desc;
            } else {
                $desc = "Куда поехать {$prep_v}{$name_v}? Города, достопримечательности и маршруты автопутешествий {$name_r}. Фото, советы и интерактивная карта региона.";
            }
            break;

        // --- ГОРОД (towns.php) ---
        case 'town':
            $s_name         = trim($data['name']);
            $s_namein       = trim($data['namein']);
            $s_namearound   = trim($data['namearound']);
            $s_nameto       = trim($data['nameto']);
            $a_name_r       = trim($data['area_name_r']);
            $meta_title     = trim($data['meta_title'] ?? '');
            $meta_desc      = trim($data['meta_description'] ?? '');

            if ($meta_title) {
                $title = $meta_title;
            } else {
                $title = "{$s_name} — что посмотреть и где остановиться | Autotravel.ru";
            }

            if ($meta_desc) {
                $desc = $meta_desc;
            } else {
                $desc = "{$s_namein}: достопримечательности, маршруты, гостиницы и советы путешественников. Что посмотреть {$s_namearound}, где остановиться и чем заняться во время поездки {$s_nameto}.";
            }
            break;

                        // --- ОБЪЕКТ / ДОСТОПРИМЕЧАТЕЛЬНОСТЬ (otklik.php) ---
        case 'otklik':
            $object_name = trim($data['object_name']);
            $town_name   = trim($data['town_name']);
            $area_name_r = trim($data['area_name_r']);
            $text        = trim(strip_tags(html_entity_decode($data['descrip'] ?? '', ENT_QUOTES, 'UTF-8')));
            $text        = preg_replace('/\s+/u', ' ', $text);
            $meta_title  = trim($data['meta_title'] ?? '');
            $meta_desc   = trim($data['meta_description'] ?? '');

            // --- title ---
            if ($meta_title) {
                $title = $meta_title;
            } else {
                $title = "{$object_name} — {$town_name}, {$area_name_r} | Autotravel.ru";
            }

            // --- description ---
            if ($meta_desc) {
                $desc = $meta_desc;
            } else {
                if ($text && preg_match('/^(.+?[.!?])(?=\s|[А-ЯA-Z]|$)/u', $text, $m)) {
                    // До первой точки / восклицания / вопроса (учитывает отсутствие пробела после точки)
                    $desc = trim($m[1]);
                } elseif ($text) {
                    // Если нет точки — обрезаем по длине
                    $desc = mb_substr($text, 0, 180);
                    $desc = preg_replace('/\s+\S*$/u', '', $desc);
                    if (mb_strlen($text) > 180) $desc .= '…';
                } else {
                    // Fallback при полном отсутствии описания
                    $desc = "{$object_name} — достопримечательность города {$town_name} ({$area_name_r}). Фото, координаты и отзывы путешественников.";
                }
            }

            // --- добавляем имя объекта, если оно отсутствует в тексте description ---
            if ($desc && !preg_match('/\b' . preg_quote($object_name, '/') . '\b/ui', $desc)) {
                $desc = "{$object_name}. {$desc}";
            }
            // --- очистка description ---
            $desc = str_replace(["\n", "\r", '"'], ' ', $desc);
            $desc = trim($desc);




            break;
    }

    // --- Open Graph (og:image) ---
    if (!empty($data['ffile'])) {
        $ogimg = "https://{$sitename}/phalbum/{$data['ffile']}.jpg";
    }

    return [
        'title' => $title,
        'desc'  => $desc,
        'ogimg' => $ogimg,
    ];
}

function build_ld($type, $data = [], $sitename = 'autotravel.ru') {
    $ld = [
        '@context' => 'https://schema.org',
        '@type'    => trim(str_replace(['itemscope itemtype="', '"'], '', $data['mk_type'] ?? 'Place')),
        'name'     => $data['object_name'] ?? '',
        'url'      => "https://{$sitename}/otklik.php/".($data['id'] ?? ''),
    ];

    if (!empty($data['desc'])) {
        $ld['description'] = $data['desc'];
    }
    if (!empty($data['ffile'])) {
        $ld['image'] = "https://{$sitename}/phalbum/{$data['ffile']}.jpg";
    }
    if (!empty($data['Nshirota']) && !empty($data['Edolgota'])) {
        $ld['geo'] = [
            '@type'    => 'GeoCoordinates',
            'latitude'  => $data['Nshirota'],
            'longitude' => $data['Edolgota']
        ];
    }
    if (!empty($data['addr'])) {
        $ld['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => $data['addr']
        ];
    }
    if (!empty($data['ratingcount']) && $data['ratingcount'] > 0) {
        $ld['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => round(min($data['ratingvalue'], 5), 1),
            'ratingCount' => (int)$data['ratingcount'],
            'bestRating'  => 5
        ];
    }
    if (!empty($data['openhours'])) {
        $ld['openingHours'] = $data['openhours'];
    }

    return "\n<script type=\"application/ld+json\">\n" .
        json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) .
        "\n</script>\n";
}

?>
