enum NotificationType {
  newDeposit,
  newPost,
  lowBalance
}

NotificationType? notificationTypeFromString(String type) {
  if (type == 'new_deposit') return NotificationType.newDeposit;
  if (type == 'new_post') return NotificationType.newPost;
  if (type == 'low_balance') return NotificationType.lowBalance;
  return null;
}

class NotificationData {
  final String id;
  final NotificationType type;
  final Map<String, dynamic> data;
  final DateTime? read_at;
  final DateTime created_at;

  NotificationData({
    required this.id,
    required this.type,
    required this.data,
    required this.read_at,
    required this.created_at
  });

  factory NotificationData.fromJson(Map<String, dynamic> json) {
    return NotificationData(
      id: json['id'],
      type: notificationTypeFromString(json['type'])!,
      data: json['data'],
      read_at: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      created_at: DateTime.parse(json['created_at'])
    );
  }
}
