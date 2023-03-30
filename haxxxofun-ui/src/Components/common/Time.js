export function toHoursAndMinutes(totalSeconds) {
    const totalMinutes = Math.floor(Math.abs(totalSeconds) / 60);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return hours > 0 ? hours + ":" + minutes : minutes;
}