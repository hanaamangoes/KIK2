<?php
// session_start(); // Uncomment this line if auth.php doesn't handle session_start()
include '../auth.php';


if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); //
    exit();
}


if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Admin';
}


$rentals = [
    ['id' => 1, 'customer' => 'Asa Zauhair', 'product' => 'Naturehike Cloud-Up 3P', 'phone' => '6285219478152', 'due_date' => '2026-04-03'], // Due Today
    ['id' => 2, 'customer' => 'Brian Santoso', 'product' => 'Osprey Aether 65L', 'phone' => '628987654321', 'due_date' => '2026-04-01'],   // Overdue
    ['id' => 3, 'customer' => 'Sarah Putri', 'product' => 'Salomon Quest 4 GTX', 'phone' => '628112233445', 'due_date' => '2026-04-10'],    // Future Due (won't show)
];

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster Admin - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* PREMIUM GREEN COLOR PALETTE (Strictly from image 2) */
        :root {
            --primary-dark: #0F2A1D;   /* Deepest Forest Green */
            --primary-mid: #375534;    /* Pine Green */
            --accent: #6B9071;         /* Muted Sage Green */
            --soft-green: #AEC3B0;     /* Pale Sage Green */
            --bg-light: #E3EED4;       /* Pale Mint Green */
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.03);
            --transition: 0.3s ease;
        }

        * { box-sizing: border-box; }
        body { 
            margin: 0;
            font-family: 'Poppins', -apple-system, sans-serif;
            color: var(--primary-dark);
            background-color: var(--bg-light);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            z-index: 1;
        }

        /* 🏔 MOUNTAIN BACKGROUND EFFECT (Blurred, Luxurious) */
        /* A premium mountain landscape placeholder (Source ethically or use local) */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: url('https://images.unsplash.com/photo-1549880181-56a44cf4a9a1?q=80&w=2000&auto=format&fit=crop'); /* Place high-res mountain here */
            background-size: cover;
            background-position: center;
            filter: blur(25px); /* Strong blurred effect */
            opacity: 0.12; /* Very subtle integration for depth */
            z-index: -1;
        }

        /* ⬅ INTERACTIVE PREMIUM SIDEBAR (Strict English, Icons) */
        .sidebar {
            width: 280px;
            background-color: var(--white);
            height: 100vh;
            position: fixed;
            padding: 40px 20px;
            border-right: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 100;
        }

        /* Modernized Logo (M-S-T) */
        .sidebar-logo {
            font-size: 30px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 50px;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        .sidebar-logo span { color: var(--accent); }

        /* Navigation item design */
        .nav-item {
            color: var(--primary-dark);
            text-decoration: none;
            padding: 15px 22px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            font-size: 15px;
            transition: var(--transition);
            margin-bottom: 8px;
        }

        /* Hover interaction */
        .nav-item:hover:not(.active) {
            background-color: #f2f7ec;
            color: var(--primary-mid);
        }

        /* Active state (Dashboard) */
        .nav-item.active {
            background-color: var(--primary-dark);
            color: var(--white);
            box-shadow: var(--shadow);
            font-weight: 600;
        }
        .nav-item.active i { color: var(--soft-green); }

        /* Icon styling */
        .nav-item i { font-size: 18px; color: var(--accent); width: 25px; text-align: center; }

        /* Separate main nav from logout */
        .main-nav { flex: 1; }

        .logout-nav {
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 25px;
        }
        .nav-item.logout { color: #ff4d4f; }
        .nav-item.logout:hover { background-color: rgba(255,77,79,0.05); }
        .nav-item.logout i { color: #ff4d4f; }


        /* ➡ MAIN CONTENT AREA DESIGN */
        .main-content {
            flex: 1;
            margin-left: 300px; /* Precise offset for interactive sidebar */
            padding: 40px;
        }

        /* PREMIUM CONTENT HEADER */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* Welcome Message (Strict English) */
        .welcome-msg { margin: 0; }
        .welcome-msg p { margin: 0; color: var(--accent); font-size: 14px; font-weight: 500; }
        .welcome-msg h1 { margin: 5px 0 0 0; font-size: 34px; font-weight: 600; color: var(--primary-dark); }

        /* User Profile & Notifications mock (as seen in image 1) */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header-actions .notif-icon { color: var(--accent); font-size: 22px; cursor: pointer; transition: var(--transition); }
        .header-actions .notif-icon:hover { color: var(--primary-mid); }
        .header-actions .user-avatar {
            width: 48px; height: 48px;
            background-color: var(--primary-dark);
            border-radius: 50%;
            color: var(--white);
            display: flex; justify-content: center; align-items: center;
            font-weight: 600; font-size: 19px;
            box-shadow: var(--shadow);
        }


        /* 📊 PROFESSIONAL STATS GRID (Replication from image 1 but English) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 45px;
        }

        .stat-card {
            background-color: var(--white);
            padding: 30px;
            border-radius: 24px;
            box-shadow: var(--shadow);
            position: relative;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-5px); }

        /* Premium styling for dark/overdue cards */
        .stat-card.overall-revenue, .stat-card.overdue { background-color: var(--primary-dark); color: var(--white); }
        .stat-card.overall-revenue h2, .stat-card.overdue h2 { color: var(--white); }
        .stat-card.active-renters, .stat-card.due-today { color: var(--primary-dark); }

        /* Typography */
        .stat-card p { font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 1.5px; margin: 0 0 10px 0; opacity: 0.7; }
        .stat-card h2 { font-size: 32px; font-weight: 700; margin: 0; color: var(--primary-dark); font-family: 'Inter', sans-serif; }
        
        /* Trend indications mock from image 1 */
        .stat-trend {
            font-size: 11px;
            position: absolute;
            top: 25px; right: 25px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .overall-revenue .stat-trend { background-color: rgba(255,255,255,0.12); color: #fff; }
        .active-renters .stat-trend { background-color: rgba(25,118,210,0.1); color: #1976D2; }
        .due-today .stat-trend { background-color: rgba(250,173,20,0.1); color: #FAAD14; }
        .overdue .stat-trend { background-color: rgba(255,255,255,0.12); color: #fff; }


        /* 📋 MAIN DATA TABLE CARD (Strict English, premium structure) */
        .table-card {
            background-color: var(--white);
            border-radius: 28px;
            padding: 35px;
            box-shadow: var(--shadow);
        }

        /* Sophisticated Table Header */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .table-header h3 { margin: 0; font-size: 21px; font-weight: 600; color: var(--primary-dark); }
        .table-header .view-all { text-decoration: none; color: var(--accent); font-size: 14px; font-weight: 500; transition: var(--transition); }
        .table-header .view-all:hover { color: var(--primary-mid); }

        /* Premium Table design */
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        
        th { text-align: left; color: var(--accent); font-weight: 500; font-size: 12px; padding-bottom: 18px; text-transform: uppercase; letter-spacing: 1px; }
        
        td { padding: 22px 0; border-bottom: 1px solid #f9f9f9; color: var(--primary-dark); }
        
        tr:last-child td { border-bottom: none; }

        /* Row background details mocked from image 1 */
        tr:nth-child(even) td { background-color: #fcfcfc; }

        /* Customer Cell with avatar mock */
        .customer-cell { display: flex; align-items: center; gap: 15px; }
        .customer-avatar-mock { width: 38px; height: 38px; border-radius: 50%; background-color: #eee; color: #aaa; display: flex; justify-content: center; align-items: center; font-weight: 600; font-size: 16px; }
        .customer-info .name { font-weight: 600; color: var(--primary-dark); }
        .customer-info .phone-str { color: var(--accent); font-size: 12px; margin-top: 3px; }

        /* Product cell sophisticated styling */
        .product-cell { font-weight: 500; }

        /* Critical Status Badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            color: var(--white);
            display: inline-block;
            letter-spacing: 0.5px;
        }

        /* WA Chat Button Design (Styled within picture 2 palette, NOT standard WA green) */
        .btn-wa {
            background-color: var(--bg-light); /* Palette pale mint */
            color: var(--primary-dark);
            padding: 9px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-wa:hover { background-color: var(--soft-green); } /* Pale sage */
        
        /* Subtle green WA icon indicator */
        .btn-wa .wa-icon { color: #25D366; font-size: 15px; } 

    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="sidebar-logo">M<span>ST</span></a>
        
        <nav class="main-nav">
            <a href="#" class="nav-item active"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="#" class="nav-item"><i class="fas fa-mountain"></i> Inventory</a>
            <a href="#" class="nav-item"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="#" class="nav-item"><i class="fas fa-user-tag"></i> Renters</a>
            <a href="#" class="nav-item"><i class="fas fa-comment-alt"></i> Messages</a>
            <a href="#" class="nav-item"><i class="fas fa-sliders-h"></i> Settings</a>
        </nav>

        <nav class="logout-nav">
            <a href="../logout.php" class="nav-item logout"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="content-header">
            <div class="welcome-msg">
                <p>Welcome back, <?php echo $_SESSION['user_name']; ?> 👋</p>
                <h1>Admin Dashboard Overview</h1>
            </div>
            <div class="header-actions">
                <i class="far fa-bell notif-icon"></i>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 2)); ?></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card overall-revenue">
                <p>Overall Revenue</p>
                <h2>$ 1.8m</h2>
                <span class="stat-trend">+21.3%</span>
            </div>
            <div class="stat-card active-renters">
                <p>Active Renters</p>
                <h2>142</h2>
                <span class="stat-trend">+10.5%</span>
            </div>
            <div class="stat-card due-today">
                <p>Due Today</p>
                <h2>01</h2>
                <span class="stat-trend due">Action Due</span>
            </div>
            <div class="stat-card overdue">
                <p>Overdue Rentals</p>
                <h2>01</h2>
                <span class="stat-trend overdue">Critical</span>
            </div>
        </div>

        <div class="table-card">
            
            <div class="table-header">
                <h3>Rentals Overdue & Due Today</h3>
                <a href="#" class="view-all">View All Orders</a>
            </div>

            <table id="duetable">
                <thead>
                    <tr>
                        <th>CUSTOMER</th>
                        <th>PRODUCT</th>
                        <th>DUE DATE</th>
                        <th>STATUS</th>
                        <th style="text-align: right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rent): 
                        $is_overdue = ($rent['due_date'] < $today);
                        $is_due_today = ($rent['due_date'] == $today);
                        
                        // Logika Format Pesan WA Otomatis (Strict English)
                        $status_label = "";
                        $status_color = "";
                        $msg = "";

                        if ($is_overdue) {
                            $status_label = "OVERDUE";
                            $status_color = "#ff4d4f"; // Standard red
                            $msg = "Hello " . $rent['customer'] . ", your rental of " . $rent['product'] . " is OVERDUE since " . date('M d, Y', strtotime($rent['due_date'])) . ". Please return or extend immediately. Thank you!";
                        } elseif ($is_due_today) {
                            $status_label = "DUE TODAY";
                            $status_color = "#faad14"; // Standard orange
                            $msg = "Hello " . $rent['customer'] . ", today is the deadline to return " . $rent['product'] . ". We expect you at the store! Thank you!";
                        } else {
                            continue; // Logic: Don't show future rentals in this critical alert table
                        }

                        // Premium WA link generation with encoded message
                        $wa_link = "https://wa.me/" . $rent['phone'] . "?text=" . urlencode($msg);
                    ?>
                    <tr>
                        <td class="customer-cell">
                            <div class="customer-avatar-mock"><?php echo strtoupper(substr($rent['customer'], 0, 1)); ?></div>
                            <div class="customer-info">
                                <div class="name"><?php echo $rent['customer']; ?></div>
                                <div class="phone-str">+<?php echo $rent['phone']; ?></div>
                            </div>
                        </td>
                        <td class="product-cell"><?php echo $rent['product']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($rent['due_date'])); ?></td>
                        <td>
                            <span class="status-badge" style="background: <?php echo $status_color; ?>;">
                                <?php echo $status_label; ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="<?php echo $wa_link; ?>" target="_blank" class="btn-wa">
                                <i class="fab fa-whatsapp wa-icon"></i> Chat
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>