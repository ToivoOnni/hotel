<?php
session_start();

$guestbookFile = 'guestbook.csv';

if (isset($_POST['toggle_mode'])) {
    $_SESSION['mode'] = ($_SESSION['mode'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$mode = $_SESSION['mode'] ?? 'light';

function saveGuestbookEntry($name, $email, $comment) {
    global $guestbookFile;
    $entry = [date('Y-m-d H:i:s'), $name, $email, $comment];
    $fp = fopen($guestbookFile, 'a');
    fputcsv($fp, $entry);
    fclose($fp);
}

function getGuestbookEntries() {
    global $guestbookFile;
    if (!file_exists($guestbookFile)) return [];
    $entries = array_map('str_getcsv', file($guestbookFile));
    return array_reverse($entries);
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Hotel</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            transition: background-color 0.3s, color 0.3s;
        }
        body.light-mode {
            background-color: #f4f4f4;
            color: #333;
        }
        body.dark-mode {
            background-color: #333;
            color: #f4f4f4;
        }
        header {position: relative; width: 100%; margin: 0; padding: 0; }
        header img { width: 100%; height: 750px; object-fit: cover;  display: block;}
        .logo { position: absolute; top: 20px; left: 50%; transform: translateX(-50%);}
        .logo img { max-width: 300px; height: auto; }
        nav { background-color: #555; padding: 10px; }
        nav a { color: white; text-decoration: none; padding: 10px; }
        .content { padding: 20px; }
        .mode-toggle {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .entry {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .slideshow-container {
            max-width: 1000px;
            position: relative;
            margin: auto;
            overflow: hidden;
        }
        .slideshow-wrapper {
            display: flex;
            transition: transform 0.5s ease;
        }
        .slide {
            flex: 0 0 100%;
        }
        .slide img {
            width: 100%;
            height: auto;
        }
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0,0,0,0.8);
        }
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }
        .prev:hover, .next:hover {
            background-color: rgba(0,0,0,0.9);
        }
    </style>
</head>
<body class="<?php echo $mode ?>-mode">
    <form method="post" class="mode-toggle">
        <input type="hidden" name="toggle_mode" value="1">
        <button type="submit"><?php echo $mode === 'light' ? '🌙' : '☀️' ?></button>
    </form>

    <header>
        <img src="hotel_images/eingang.png" alt="AI Hotel header">
    </header>
    <div class="logo">
        <img src="hotel_images/ai_logo.png" alt="AI Hotel Logo">
    </div>
    <nav>
        <a href="?page=home">Home</a>
        <a href="?page=gallery">Galerie</a>
        <a href="?page=prices">Preise</a>
        <a href="?page=booking">Buchung</a>
        <a href="?page=guestbook">Gästebuch</a>
    </nav>
    <div class="content">
        <?php
        switch($page) {
            case 'home':
                echo "<h1>Herzlich Willkommen im AI Hotel!</h1>";
                $willkommenstext = <<<TEXT
                <p>Wir freuen uns, Sie im AI Hotel willkommen zu heißen – Ihrem Rückzugsort für Entspannung und Genuss! Ob Sie geschäftlich oder privat reisen, unser Hotel bietet Ihnen den perfekten Rahmen für einen unvergesslichen Aufenthalt.</p>
        
                <p>Entdecken Sie unsere Annehmlichkeiten:
                Komfortable Zimmer: Jedes Zimmer ist modern und stilvoll eingerichtet, ausgestattet mit allem, was Sie für einen angenehmen Aufenthalt benötigen.
                Exzellente Lage: Genießen Sie die Nähe zu den besten Sehenswürdigkeiten, Restaurants und Verkehrsanbindungen.
                Hervorragender Service: Unser freundliches Team steht Ihnen jederzeit zur Verfügung, um sicherzustellen, dass es Ihnen an nichts fehlt.</p>
        
                <p>Einzigartige Erlebnisse warten auf Sie!</p>
                <p>Lassen Sie sich von unserer Bildergalerie inspirieren und entdecken Sie die Schönheit unserer Hotelanlage. Ob entspannende Momente in unseren stilvollen Zimmern oder aufregende Abenteuer in der Umgebung – bei uns finden Sie alles!</p>                                              
        
                <p>Buchen Sie jetzt Ihren Aufenthalt!
                Nutzen Sie unser einfaches Buchungssystem, um Ihren Aufenthalt zu planen. Geben Sie Ihre Anreise- und Abreisedaten an, wählen Sie die Anzahl der Personen aus und genießen Sie eine reibungslose Buchungserfahrung.</p>
                
                <p>Wir freuen uns auf Ihren Besuch!
                Ihr AI Hotel Team wünscht Ihnen einen angenehmen Aufenthalt. Bei Fragen oder Wünschen zögern Sie nicht, uns zu kontaktieren – wir sind hier, um Ihnen zu helfen!</p>
                TEXT;
                echo $willkommenstext;
                break;
            
            case 'gallery':
                echo "<h1>Bildergalerie</h1>";
                echo '<div class="slideshow-container">
                        <div class="slideshow-wrapper">
                            <!-- Bilder werden hier dynamisch eingefügt -->
                        </div>
                        <a class="prev" onclick="changeSlide(-1)">&#10094;</a>
                        <a class="next" onclick="changeSlide(1)">&#10095;</a>
                      </div>';
                break;
            
            case 'prices':
                echo "<h1>Preisliste</h1>";
                echo "<ul>
                    <li>Einzelbett: 80€ pro Nacht</li>
                    <li>Doppelbett-Zimmer: 120€ pro Nacht</li>
                    <li>Ehezimmer: 150€ pro Nacht</li>
                    <li>Familien-Zimmer: 200€ pro Nacht</li>
                    <li>Präsidenten Suite: 500€ pro Nacht</li>
                </ul>";
                break;
            
            case 'booking':
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book'])) {
                    echo "<h2>Buchungsbestätigung</h2>";
                    echo "<p>Ihre Reservierung ist bei uns eingegangen, {$_POST['name']}, für den Zeitraum {$_POST['checkin']} - {$_POST['checkout']}. Wir wünschen Ihnen bei uns einen angenehmen Aufenthalt! Ihr AI-Hotel Team</p>";
                } else {
                    echo "<h1>Buchung</h1>";
                    echo "<form method='post'>
                        <input type='text' name='name' placeholder='Name' required><br>
                        <input type='email' name='email' placeholder='E-Mail' required><br>
                        <input type='date' name='checkin' required><br>
                        <input type='date' name='checkout' required><br>
                        <input type='number' name='adults' placeholder='Erwachsene' required><br>
                        <input type='number' name='children' placeholder='Kinder'><br>
                        <input type='number' name='dogs' placeholder='Hunde'><br>
                        <input type='submit' name='book' value='Buchen'>
                    </form>";
                }
                break;
            
            case 'guestbook':
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
                    saveGuestbookEntry($_POST['name'], $_POST['email'], $_POST['comment']);
                    echo "<p>Vielen Dank für Ihren Eintrag!</p>";
                }
                echo "<h1>Gästebuch</h1>";
                echo "<form method='post'>
                    <input type='text' name='name' placeholder='Name' required><br>
                    <input type='email' name='email' placeholder='E-Mail' required><br>
                    <textarea name='comment' placeholder='Ihr Kommentar' required></textarea><br>
                    <input type='submit' value='Eintrag hinzufügen'>
                </form>";
                
                $entries = getGuestbookEntries();
                foreach ($entries as $entry) {
                    $name = isset($entry[1]) ? $entry[1] : 'Unbekannter Name'; 
                    $comment = isset($entry[3]) ? $entry[3] : 'Kein Kommentar'; 
                    $email = isset($entry[2]) ? $entry[2] : 'Keine E-Mail';     
                    $date = isset($entry[0]) ? $entry[0] : 'Unbekanntes Datum';

                    echo "<div class='entry'>
                        <h3>{$name}</h3>
                        <p>{$comment}</p>
                        <small>{$date} - {$email}</small>
                    </div>";
                }
                break;
        }
        ?>
    </div>

    <script>
    let slideIndex = 0;
    const slideshowWrapper = document.querySelector('.slideshow-wrapper');

    function createSlideshow() {
        if (!slideshowWrapper) return;

        const imageCategories = [
            'Einzelbett', 'Doppelbett', 'Familien', 'Präsident', 'Wellness'
        ];
        const singleImages = ['Bar', 'Cocktail', 'Pool_Landschaft', 'Sportanlage'];
        let allImages = [];

        imageCategories.forEach(category => {
            const count = category === 'Präsident' ? 2 :
                          category === 'Wellness' ? 3 : 4;
            for (let i = 1; i <= count; i++) {
                allImages.push(`hotel_images/${category}/${category}${i}.png`);
            }
        });

        singleImages.forEach(image => {
            allImages.push(`hotel_images/${image}.png`);
        });

        allImages.forEach((image, index) => {
            const slide = document.createElement('div');
            slide.className = 'slide';
            const img = document.createElement('img');
            img.src = image;
            img.alt = `Bild ${index + 1}`;
            img.onerror = () => console.error(`Failed to load image: ${image}`);
            slide.appendChild(img);
            slideshowWrapper.appendChild(slide);
        });

        showSlides();
    }

    function changeSlide(n) {
        slideIndex += n;
        showSlides();
    }

    function showSlides() {
        const slides = document.querySelectorAll('.slide');
        if (slideIndex >= slides.length) slideIndex = 0;
        if (slideIndex < 0) slideIndex = slides.length - 1;
        
        slideshowWrapper.style.transform = `translateX(-${slideIndex * 100}%)`;
    }

    window.addEventListener('load', createSlideshow);
    </script>
</body>
</html>