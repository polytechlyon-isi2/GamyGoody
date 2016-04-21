
--
-- Database: `gamygoody`
--

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`art_id`, `art_title`, `art_content`, `art_price`, `game_id`) VALUES
(2, 'Jinx', 'petite figurine', '17.00', 1),
(3, 'Jinx pirotechnicienne', 'petite figurine', '19.00', 1),
(4, 'Porte clé Hearthstone', 'petit porte clé', '6.30', 2);

--
-- Dumping data for table `article_image`
--

INSERT INTO `article_image` (`id`, `article_id`, `image_id`, `level`) VALUES
(2, 2, 4, 0),
(3, 2, 5, 1),
(4, 3, 6, 0),
(5, 3, 7, 1);

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`game_id`, `game_title`, `game_logo_id`, `game_bg_id`) VALUES
(1, 'League of Legends', 1, 2),
(2, 'Hearthstone', 8, 9);

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`img_id`, `img_url`, `img_alt`) VALUES
(1, 'png', 'League_of_Legends_logo.png'),
(2, 'jpeg', 'akali_vs_baron_4.jpg'),
(4, 'jpeg', 'jinx-front-shot---retouched_1.jpg'),
(5, 'jpeg', 'jinx_zbox.jpg'),
(6, 'png', 'fcj_v2_dentalwork_2.png'),
(7, 'png', 'jinx_large_new_top-_1_.png'),
(8, 'png', 'Hearthstone-Logo.png'),
(9, 'jpeg', 'Hearthstone legend Warcraft Heroes HD Wallpapers 2.jpg');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_surname`, `user_firstname`, `user_address`, `user_city`, `user_password`, `user_salt`, `user_role`, `user_mail`) VALUES
(1, 'Atles', 'Etienne', 'DEBARD', 'Rachassac', 'St Germain Laprade', 'HvN+3yDQ46cfhEr7/u84cQBHwosVoC9mXGg+eiBa+CgFH4DpyrScqfTTUXwiGQ5sTKU0saN9CXabxnhlMK1z/Q==', '54e78a61948aca8ee5bc679', 'ROLE_ADMIN', 'etienne1995@orange.fr');
