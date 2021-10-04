CREATE TABLE `otp_configuration` (
  `id` int(11) NOT NULL,
  `twilio_sid` text DEFAULT NULL,
  `twilio_auth_token` text DEFAULT NULL,
  `twilio_mobile_number` text DEFAULT NULL,
  `msg_authkey` varchar(255) DEFAULT NULL,
  `msg_template_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `otp_configuration` (`id`, `twilio_sid`, `twilio_auth_token`, `twilio_mobile_number`, `msg_authkey`, `msg_template_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'twilio_sid', 'twilio_auth_token', 'twilio_mobile_number', NULL, NULL, 'twilio', 1, '2021-07-24 11:53:44', '2021-07-26 08:37:59'),
(2, NULL, NULL, NULL, 'msg_authkey', 'msg_template_id', 'msg91', 0, '2021-07-26 13:26:13', '2021-07-26 08:37:55');


ALTER TABLE `otp_configuration`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `otp_configuration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;