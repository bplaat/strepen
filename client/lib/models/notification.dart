class NotificationData {
  final String id;
  final String type;
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
      type: json['type'],
      data: json['data'],
      read_at: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      created_at: DateTime.parse(json['created_at'])
    );
  }
}
